<?php

$errors = [];
$warnings = [];

echo "=== Building MapQuery Package ===\n\n";

echo "Step 1: Running tests...\n";
$phpunitPath = is_file('vendor/bin/phpunit.bat') ? 'vendor/bin/phpunit.bat' : 'vendor/bin/phpunit';
$testCommand = "php $phpunitPath";
runCommand($testCommand, $output, $exitCode);

if ($exitCode !== 0) {
    $errors[] = "Tests failed with exit code $exitCode";
    echo "❌ Tests FAILED!\n";
    echo $output . "\n";
    exit(1);
}

echo "✅ All tests passed!\n\n";

echo "Step 2: Validating composer.json...\n";
runCommand('composer validate --no-check-publish', $output, $exitCode);

if ($exitCode !== 0) {
    $warnings[] = "Composer validation failed";
    echo "⚠️  Warning: composer.json validation issues\n";
    echo $output . "\n";
} else {
    echo "✅ composer.json is valid\n\n";
}

echo "Step 3: Generating autoload files...\n";
runCommand('composer dump-autoload --optimize', $output, $exitCode);

if ($exitCode !== 0) {
    $errors[] = "Failed to generate autoload files";
    echo "❌ Failed to generate autoload files\n";
    echo $output . "\n";
    exit(1);
}

echo "✅ Autoload files generated\n\n";

echo "Step 4: Creating package archive...\n";

if (!is_dir('dist')) {
    mkdir('dist', 0755, true);
}

runCommand('composer archive --format=zip --dir=dist', $output, $exitCode);

if ($exitCode !== 0) {
    $errors[] = "Failed to create archive";
    echo "❌ Failed to create archive\n";
    echo $output . "\n";
    exit(1);
}

preg_match('/Created:\s*(.+\.zip)/', $output, $matches);
if (isset($matches[1]) && !empty($matches[1]) && is_string($matches[1])) {
    $archiveFile = trim($matches[1]);
    if (file_exists($archiveFile)) {
        $fileSize = filesize($archiveFile);
        $fileSizeMB = round($fileSize / 1024 / 1024, 2);
        $fileSizeKB = round($fileSize / 1024, 2);

        echo "✅ Package archive created: $archiveFile\n";
        if ($fileSizeMB >= 1) {
            echo "   Size: $fileSizeMB MB\n\n";
        } else {
            echo "   Size: $fileSizeKB KB\n\n";
        }
    } else {
        echo "✅ Package archive created\n\n";
    }
} else {
    echo "✅ Package archive created\n\n";
}

if (!empty($warnings)) {
    echo "⚠️  Warnings:\n";
    foreach ($warnings as $warning) {
        echo "   - $warning\n";
    }
    echo "\n";
}

if (empty($errors)) {
    echo "=== Build completed successfully! ===\n";
    exit(0);
} else {
    echo "=== Build completed with errors ===\n";
    foreach ($errors as $error) {
        echo "❌ $error\n";
    }
    exit(1);
}

function runCommand(string $command, &$output, &$exitCode): bool
{
    $output = '';
    $exitCode = 0;

    $descriptorspec = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w']
    ];

    $process = proc_open($command, $descriptorspec, $pipes);

    if (!is_resource($process)) {
        $exitCode = 1;
        return false;
    }

    fclose($pipes[0]);

    $stdout = stream_get_contents($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);

    $output = $stdout . $stderr;
    $exitCode = proc_close($process);

    return $exitCode === 0;
}
