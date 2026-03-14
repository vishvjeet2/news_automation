<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AIassistentService
{
    public function analyze(Request $request)
    {

        $apiKey = env('GROQ_API_KEY');

        $news = $request->raw_content;

        $prompt = "
                    You are an AI news verification assistant, multilingual newsroom editor, and social media content generator.

                    Your job is to analyze a full news article, correct it if necessary, verify safety and credibility, and generate structured news content suitable for posting on X.

                    IMPORTANT:
                    The tone must follow professional BREAKING NEWS style used by Hindi news channels.

                    CITY DETECTION RULE:
                    You must extract the CITY name mentioned in the article.

                    City Detection Guidelines:
                    - Identify the primary city or location where the event occurred.
                    - The city usually appears at the beginning of Hindi news (example: 'बाड़मेर:', 'जयपुर:', 'दिल्ली:').
                    - If multiple places appear, select the **main city where the incident happened**.
                    - If no city is found, return null.

                    The detected city must also be used:
                    - At the beginning of the headline
                    - Inside the caption
                    - Inside the description
                    - Inside the Hindi hashtag

                    News Tone Guidelines:
                    - Headline should feel like a BREAKING NEWS alert.
                    - Use concise, impactful language.
                    - Mention the location using the detected city (example: 'बाड़मेर:').
                    - Maintain a neutral, journalistic tone.
                    - Avoid exaggeration and speculation.
                    - Do not add facts not present in the article.

                    Tasks:
                    1. Detect the language automatically.
                    2. Extract the CITY name from the article.
                    3. Check grammar, spelling, and sentence structure.
                    4. If grammar errors exist, provide a corrected version of the article.
                    5. Maintain the original language when correcting (especially Hindi).
                    6. Ensure the article is written in THIRD PERSON narration.
                    7. Ensure the tone is neutral, objective, and journalistic.
                    8. Evaluate credibility and detect exaggerated or suspicious claims.
                    9. Detect harmful or unsafe content.
                    10. Generate a BREAKING NEWS style headline using the city.
                    11. Generate a CAPTION summarizing the key information in approximately **40 words**.
                    12. Generate a DESCRIPTION explaining more details of the story in approximately **50 words**.
                    13. Generate one accurate Hindi hashtag using the city or topic.

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
                    5. Respect word limits strictly.
                    6. Do not use emojis.
                    7. Return valid JSON only.
                    8. No explanation outside JSON.

                    Caption Rules:
                    - Caption must be approximately **40 words**.
                    - Caption should give a quick breaking-news summary.
                    - Caption must contain **English hashtags at the end**.
                    - Maximum **6 hashtags in caption**.

                    Description Rules:
                    - Description must be approximately **50 words**.
                    - Description must explain the story in more detail than the caption.
                    - Must include key facts such as location, action, and important names.
                    - Must remain factual and neutral.

                    Hashtag Rules:
                    - The \"hashtags\" JSON field must contain **ONE Hindi hashtag only**.
                    - The hashtag should preferably include the city name or topic.
                    - Do NOT repeat English hashtags from caption.

                    Return ONLY valid JSON in this format:

                    {
                    \"language_detected\": \"language name\",
                    \"city\": \"detected city name or null\",
                    \"headline\": \"breaking style news headline starting with city name\",
                    \"caption\": \"40-word breaking-news caption with English hashtags\",
                    \"description\": \"50-word news explanation with more detail\",
                    \"hashtags\": [\"#हिंदीहैशटैग\"],

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
                    " . $news;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.groq.com/openai/v1/chat/completions', [
            "model" => "llama-3.1-8b-instant",
            "messages" => [
                [
                    "role" => "system",
                    "content" => "You are a professional news editor. You only respond in valid JSON."
                ],
                [
                    "role" => "user",
                    "content" => $prompt
                ]
            ],
            "temperature" => 0.2
        ]);

        $data = $response->json();

        if (isset($data['error'])) {
            return response()->json(['error' => $data['error']['message'] ?? 'AI API Error'], 500);
        }

        $aiContent = $data['choices'][0]['message']['content'] ?? '';

        // --- CRITICAL: Clean JSON blocks if AI includes them ---
        $aiContent = trim($aiContent);
        $aiContent = preg_replace('/^```json|```$/m', '', $aiContent);

        $decoded = json_decode($aiContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json([
                "error" => "AI did not return valid JSON",
                "raw_response" => $aiContent
            ], 422);
        }

        // --- Map the response to your Frontend keys ---
        return response()->json([
            'headline'    => $decoded['headline'] ?? '',
            'caption'     => $decoded['caption'] ?? '',
            'city'    => $decoded['city']    ?? '',
            'description' => $decoded['description'] ?? '',
            'hashtag'     => $decoded['hashtags'][0] ?? '', // Sends single hashtag to your form
            'risk_flag'   => ($decoded['fake_news_risk'] === 'High' || $decoded['decision'] === 'Reject'),
            'risk_reason' => $decoded['risk_reason'] ?? 'Potential sensitivity detected in content.',
            'language'    => $decoded['language_detected'] ?? 'Unknown'
        ]);
    }
}
