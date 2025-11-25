class WebPushManager {
  constructor() {
    // CHAVE PÚBLICA VAPID - obrigatória para autenticar com o servidor push
    this.publicKey = "BEl62iUYgUivxIkv69yViEuiBIa40HI0DzCp4CMcpW3gBC4HfcKNdXAwGZsVOMLwk77XVLJmNhvOuHd4xzipxm8"
    this.isSupported = "serviceWorker" in navigator && "PushManager" in window
    this.init()
  }

  async init() {
    if (!this.isSupported) {
      console.warn("Web Push não é suportado neste navegador")
      return
    }

    try {
      // 1. REGISTRAR SERVICE WORKER - sem isso, push não funciona
      const registration = await navigator.serviceWorker.register("/sw.js")
      console.log("Service Worker registrado:", registration)

      // 2. VERIFICAR PERMISSÕES - obrigatório para mostrar notificações
      if (Notification.permission === "granted") {
        this.setupPushSubscription(registration)
      }
    } catch (error) {
      console.error("Erro ao registrar Service Worker:", error)
    }
  }

  async requestPermission() {
    if (!this.isSupported) {
      alert("Seu navegador não suporta notificações push")
      return false
    }

    // 3. SOLICITAR PERMISSÃO DO USUÁRIO - sem isso, nada funciona
    const permission = await Notification.requestPermission()

    if (permission === "granted") {
      console.log("Permissão concedida para notificações")
      const registration = await navigator.serviceWorker.ready
      await this.setupPushSubscription(registration)
      return true
    } else {
      console.log("Permissão negada para notificações")
      return false
    }
  }

  async setupPushSubscription(registration) {
    try {
      // 4. CRIAR SUBSCRIPTION - conecta o navegador com o servidor push
      let subscription = await registration.pushManager.getSubscription()

      if (!subscription) {
        subscription = await registration.pushManager.subscribe({
          userVisibleOnly: true, // Sempre mostrar notificação visível
          applicationServerKey: this.urlBase64ToUint8Array(this.publicKey), // Chave VAPID
        })
      }

      // 5. ENVIAR SUBSCRIPTION PARA SERVIDOR - Laravel precisa saber onde enviar
      await this.sendSubscriptionToServer(subscription)
      console.log("Push subscription configurada:", subscription)
    } catch (error) {
      console.error("Erro ao configurar push subscription:", error)
    }
  }

  async sendSubscriptionToServer(subscription) {
    try {
      // 6. SALVAR NO BANCO DE DADOS - Laravel armazena para enviar depois
      const response = await fetch("/api/push-subscriptions", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
        },
        body: JSON.stringify({
          subscription: subscription.toJSON(),
        }),
      })

      if (!response.ok) {
        throw new Error("Erro ao salvar subscription no servidor")
      }

      console.log("Subscription salva no servidor")
    } catch (error) {
      console.error("Erro ao enviar subscription:", error)
    }
  }

  // 7. CONVERTER CHAVE VAPID - formato específico do navegador
  urlBase64ToUint8Array(base64String) {
    const padding = "=".repeat((4 - (base64String.length % 4)) % 4)
    const base64 = (base64String + padding).replace(/-/g, "+").replace(/_/g, "/")

    const rawData = window.atob(base64)
    const outputArray = new Uint8Array(rawData.length)

    for (let i = 0; i < rawData.length; ++i) {
      outputArray[i] = rawData.charCodeAt(i)
    }
    return outputArray
  }

  // 8. TESTE LOCAL - para verificar se funciona
  async testNotification() {
    if (Notification.permission === "granted") {
      new Notification("Teste de Notificação", {
        body: "Esta é uma notificação de teste da farmácia!",
        icon: "/images/pharmacy-icon.png",
        badge: "/images/badge-icon.png",
      })
    } else {
      console.log("Permissão necessária para notificações")
    }
  }
}

// 9. INICIALIZAR AUTOMATICAMENTE - quando a página carrega
document.addEventListener("DOMContentLoaded", () => {
  window.webPushManager = new WebPushManager()

  // 10. CONECTAR COM BOTÕES DA INTERFACE
  const enableBtn = document.getElementById("enable-notifications")
  const testBtn = document.getElementById("test-notification")

  if (enableBtn) {
    enableBtn.addEventListener("click", () => {
      window.webPushManager.requestPermission()
    })
  }

  if (testBtn) {
    testBtn.addEventListener("click", () => {
      window.webPushManager.testNotification()
    })
  }
})
