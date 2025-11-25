<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LegalDocument;
use Illuminate\Http\Request;

class LegalDocumentController extends Controller
{
    public function show($type)
    {
        try {
            $document = LegalDocument::where('type', $type)
                ->where('is_active', true)
                ->latest()
                ->first();

            if (!$document) {
                return response()->json([
                    'error' => 'Documento não encontrado'
                ], 404);
            }

            return response()->json([
                'id' => $document->id,
                'type' => $document->type,
                'title' => $document->title,
                'content' => $document->content,
                'version' => $document->version,
                'created_at' => $document->created_at,
                'updated_at' => $document->updated_at
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro interno do servidor'
            ], 500);
        }
    }

    public function showTerms()
    {
        $document = LegalDocument::where('type', 'terms_of_use')
            ->where('is_active', true)
            ->latest()
            ->first();

        if (!$document) {
            abort(404, 'Termos de Uso não encontrados');
        }

        return view('legal.document', [
            'document' => $document,
            'pageTitle' => 'Termos de Uso'
        ]);
    }

    public function showPrivacy()
    {
        $document = LegalDocument::where('type', 'privacy_policy')
            ->where('is_active', true)
            ->latest()
            ->first();

        if (!$document) {
            abort(404, 'Política de Privacidade não encontrada');
        }

        return view('legal.document', [
            'document' => $document,
            'pageTitle' => 'Política de Privacidade'
        ]);
    }
}
