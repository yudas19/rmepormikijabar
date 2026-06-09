<?php

$transcript = file('/Users/digisekre/.gemini/antigravity-ide/brain/1f793d78-bf1e-4706-a641-8ba47caf9e6f/.system_generated/logs/transcript.jsonl');
foreach ($transcript as $line) {
    $data = json_decode($line, true);
    if (isset($data['step_index']) && $data['step_index'] >= 220 && $data['step_index'] <= 280) {
        echo "=== STEP {$data['step_index']} ===\n";
        echo "Type: {$data['type']}\n";
        if (isset($data['thinking'])) {
            echo 'Thinking: '.substr($data['thinking'], 0, 100)."...\n";
        }
        if (isset($data['tool_calls'])) {
            foreach ($data['tool_calls'] as $tc) {
                echo "Tool Call: {$tc['name']}\n";
                if ($tc['name'] == 'capture_browser_console_logs') {
                    echo 'Args: '.json_encode($tc['args'])."\n";
                }
            }
        }
        if (isset($data['content'])) {
            echo 'Content snippet: '.substr($data['content'], 0, 500)."\n";
        }
        echo "\n";
    }
}
