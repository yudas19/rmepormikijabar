<?php

$transcript = file('/Users/digisekre/.gemini/antigravity-ide/brain/1f793d78-bf1e-4706-a641-8ba47caf9e6f/.system_generated/logs/transcript.jsonl');
foreach ($transcript as $line) {
    $data = json_decode($line, true);
    if (isset($data['step_index']) && $data['step_index'] == 606) {
        echo "=== STEP 606 ===\n";
        echo json_encode($data, JSON_PRETTY_PRINT)."\n";
    }
}
