// SERVICE WORKER - RODA EM BACKGROUND, MESMO COM SITE FECHADO
const CACHE_NAME = "farmacia-v1"
const urlsToCache = ["/", "/css/app.css", "/js/app.js", "/images/pharmacy-icon.png", "/images/badge-icon.png"]

// 1. INSTALAR SERVICE WORKER
self.addEventListener("install", (event) => {
  console.log("Service Worker instalando...")
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      console.log("Cache aberto")
      return cache.addAll(urlsToCache) // Cache arquivos importantes
    }),
  )
})

// 2. ATIVAR SERVICE WORKER
self.addEventListener("activate", (event) => {
  console.log("Service Worker ativado")
  event.waitUntil(
    caches.keys().then((cacheNames) =>
      Promise.all(
        cacheNames.map((cacheName) => {
          if (cacheName !== CACHE_NAME) {
            console.log("Removendo cache antigo:", cacheName)
            return caches.delete(cacheName)
          }
        }),
      ),
    ),
  )
})

// 3. INTERCEPTAR REQUISIÇÕES (para funcionar offline)
self.addEventListener("fetch", (event) => {
  event.respondWith(
    caches.match(event.request).then((response) => {
      if (response) {
        return response // Retorna do cache
      }
      return fetch(event.request) // Busca na internet
    }),
  )
})

// 4. ⭐ RECEBER PUSH MESSAGES - MAIS IMPORTANTE!
self.addEventListener("push", (event) => {
  console.log("Push message recebida:", event)

  // Dados padrão da notificação
  let notificationData = {
    title: "Farmácia Online",
    body: "Você tem uma nova notificação",
    icon: "/images/pharmacy-icon.png",
    badge: "/images/badge-icon.png",
    tag: "farmacia-notification",
    requireInteraction: true, // Não desaparece sozinha
    actions: [
      {
        action: "view",
        title: "Ver Detalhes",
        icon: "/images/view-icon.png",
      },
      {
        action: "close",
        title: "Fechar",
        icon: "/images/close-icon.png",
      },
    ],
  }

  // Processar dados enviados pelo Laravel
  if (event.data) {
    try {
      const data = event.data.json()
      notificationData = {
        ...notificationData,
        ...data, // Sobrescreve com dados do servidor
      }
    } catch (e) {
      console.error("Erro ao parsear dados da notificação:", e)
    }
  }

  // 5. MOSTRAR A NOTIFICAÇÃO
  event.waitUntil(self.registration.showNotification(notificationData.title, notificationData))
})

// 6. LIDAR COM CLIQUES NA NOTIFICAÇÃO
self.addEventListener("notificationclick", (event) => {
  console.log("Notificação clicada:", event)

  event.notification.close() // Fechar notificação

  if (event.action === "view") {
    // Botão "Ver Detalhes" clicado
    const urlToOpen = event.notification.data?.url || "/"

    event.waitUntil(
      clients
        .matchAll({
          type: "window",
        })
        .then((clientList) => {
          // Verificar se já existe uma janela aberta
          for (let i = 0; i < clientList.length; i++) {
            const client = clientList[i]
            if (client.url === urlToOpen && "focus" in client) {
              return client.focus() // Focar na janela existente
            }
          }

          // Abrir nova janela
          if (clients.openWindow) {
            return clients.openWindow(urlToOpen)
          }
        }),
    )
  } else if (event.action === "close") {
    // Botão "Fechar" clicado
    console.log("Notificação fechada pelo usuário")
  } else {
    // Clique direto na notificação (sem botão)
    const urlToOpen = event.notification.data?.url || "/"
    event.waitUntil(clients.openWindow(urlToOpen))
  }
})

// 7. NOTIFICAÇÃO FECHADA (sem clique)
self.addEventListener("notificationclose", (event) => {
  console.log("Notificação fechada:", event)
})
