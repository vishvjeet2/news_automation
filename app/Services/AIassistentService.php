<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

class AIassistentService
{
    public function testGroq()
    {
        $apiKey = env('GROQ_API_KEY');

        $news = "बढ़ती आबादी की आवश्यकताओं को केन्द्र में रखकर बनेंगी सुनियोजित विकास की योजनाएं  – मुख्यमंत्री श्री भजनलाल शर्मा जयपुर, 10 मार्च। मुख्यमंत्री भजनलाल शर्मा ने कहा कि विकसित राजस्थान – 2047 के लक्ष्य के क्रम में राज्य के प्रत्येक ग्राम और वार्ड क..";

        $prompt = "
            You are an AI news verification assistant and multilingual newsroom editor.

            Your job is to analyze and correct a news article while maintaining professional newsroom standards.

            Tasks:
            1. Detect the language automatically.
            2. Check grammar, spelling, and sentence structure.
            3. If grammar errors exist, provide a corrected version of the article.
            4. Maintain the original language when correcting (especially Hindi).
            5. Ensure the article is written in THIRD PERSON narration.
            6. Ensure the tone is neutral, objective, and journalistic.
            7. Evaluate credibility and detect exaggerated or suspicious claims.
            8. Detect harmful or unsafe content.

            Editorial Formatting Rule:
            If any name contains honorifics like:
            - श्री
            - श्रीमती
            - श्रीमान

            Remove these words from the name because they are not used in professional news writing.

            Example:
            'श्री नरेंद्र मोदी' → 'नरेंद्र मोदी'

            Safety Checks:
            Check for:
            - Hate speech
            - Communal tension
            - Defamation
            - Misleading framing

            STRICT RULES:
            1. Do not add new facts.
            2. Do not change the meaning of the article.
            3. Do not exaggerate or sensationalize.
            4. Keep tone neutral and journalistic.
            5. Respect character limits strictly.
            6. Do not use emojis.
            7. Return valid JSON only.
            8. No explanation outside JSON.

            Return ONLY valid JSON in this format:

            {
            \"language_detected\": \"language name\",
            \"credibility_score\": number (1-10),
            \"fake_news_risk\": \"Low | Medium | High\",
            \"grammar_score\": number (1-10),
            \"decision\": \"Approve | Needs Edit | Reject\",
            \"safety_flags\": {
                \"hate_speech\": true | false,
                \"communal_tension\": true | false,
                \"defamation\": true | false,
                \"misleading_framing\": true | false
            },
            \"grammar_issues\": [\"issue1\", \"issue2\"],
            \"corrected_text\": \"corrected version of the article with honorifics removed and written in third person\",
            \"suggestions\": [\"suggestion1\", \"suggestion2\"]
            }

            Rules for corrections:
            - Preserve the original language.
            - If the article is Hindi, return corrected Hindi text.
            - Remove honorific words like 'श्री', 'श्रीमती', 'श्रीमान'.
            - Ensure the article is written in third person.
            - Do not translate unless necessary.
            - Do not invent facts.

            Article:
            ".$news;

            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                "model" => "llama-3.1-8b-instant",
                "messages" => [
                    [
                        "role" => "system",
                        "content" => "You are a professional news editor and fact checker."
                    ],
                    [
                        "role" => "user",
                        "content" => $prompt
                    ]
                ],
                "temperature" => 0.2
            ]);
            
            $data = $response->json();

        if(isset($data['error'])){
            return response()->json($data);
        }

        $aiResponse = $data['choices'][0]['message']['content'] ?? null;

        // Try to decode JSON
        $decoded = json_decode($aiResponse, true);

        if(json_last_error() !== JSON_ERROR_NONE){
            return response()->json([
                "error" => "AI did not return valid JSON",
                "raw_response" => $aiResponse
            ]);
        }

        return response()->json($decoded);
    }
}