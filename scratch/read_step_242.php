<?php

$transcript = file('/Users/digisekre/.gemini/antigravity-ide/brain/1f793d78-bf1e-4706-a641-8ba47caf9e6f/.system_generated/logs/transcript.jsonl');
foreach ($transcript as $line) {
    if (strpos($line, 'capture_browser_console_logs') !== false) {
        $data = json_decode($line, true);
        echo "=== STEP {$data['step_index']} ===\n";
        echo "Source: {$data['source']}, Type: {$data['type']}, Status: {$data['status']}\n";
        if (isset($data['content'])) {
            echo 'Content: '.substr($data['content'], 0, 1000)."\n";
        }
        echo "\n";
    }
}
