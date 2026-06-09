<?php

$transcript = file('/Users/digisekre/.gemini/antigravity-ide/brain/1f793d78-bf1e-4706-a641-8ba47caf9e6f/.system_generated/logs/transcript.jsonl');
foreach ($transcript as $line) {
    $data = json_decode($line, true);
    if (isset($data['type']) && $data['type'] == 'RUN_COMMAND' && isset($data['content'])) {
        // Skip
    }
    if (isset($data['step_index']) && $data['step_index'] > 600) {
        // Let's print tool calls and response content
        if (isset($data['tool_calls'])) {
            foreach ($data['tool_calls'] as $tc) {
                echo "Step {$data['step_index']} Tool: {$tc['name']}\n";
            }
        }
        if (isset($data['status']) && $data['status'] == 'DONE' && isset($data['content'])) {
            echo "Step {$data['step_index']} Content snippet:\n".substr($data['content'], 0, 500)."\n\n";
        }
    }
}
