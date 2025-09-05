<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Stichoza\GoogleTranslate\GoogleTranslate;

class TranslationController extends Controller
{
    public function translate(Request $request, string $formLang, string $toLang) {
        $request->validate([
            'source' => 'required'
        ]);
        $translator = new GoogleTranslate();
        $translator->setTarget($toLang);
        $translatedString = $translator->translate($request->source);
        return response()->json([
            'original' => $request->source,
            'translated' => $translatedString
        ]);
    }
}
