<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ValidatePilotMembersCommand extends Command
{
    protected $signature = 'pilot:validate-members {file : UTF-8 CSV file to validate without importing}';
    protected $description = 'Validate pilot member data and report errors without writing to the database';

    public function handle(): int
    {
        $path = (string) $this->argument('file');
        if (! is_readable($path)) {
            $this->error("Cannot read {$path}.");
            return self::FAILURE;
        }

        $handle = fopen($path, 'rb');
        if ($handle === false) return self::FAILURE;
        $required = ['external_reference', 'first_name', 'last_name', 'email', 'phone', 'date_of_birth', 'category_code'];
        $headers = array_map(fn ($value) => trim((string) $value), fgetcsv($handle) ?: []);
        $missing = array_diff($required, $headers);
        if ($missing !== []) {
            $this->error('Missing columns: '.implode(', ', $missing));
            fclose($handle);
            return self::FAILURE;
        }

        $seen = [];
        $errors = [];
        $rowNumber = 1;
        while (($values = fgetcsv($handle)) !== false) {
            $rowNumber++;
            if (count($headers) !== count($values)) {
                $errors[] = "Row {$rowNumber}: column count does not match header.";
                continue;
            }
            $row = array_combine($headers, $values);
            foreach ($required as $column) {
                if (trim((string) $row[$column]) === '') $errors[] = "Row {$rowNumber}: {$column} is required.";
            }
            if (filter_var($row['email'], FILTER_VALIDATE_EMAIL) === false) $errors[] = "Row {$rowNumber}: invalid email.";
            $date = \DateTimeImmutable::createFromFormat('!Y-m-d', (string) $row['date_of_birth']);
            if (! $date || $date->format('Y-m-d') !== $row['date_of_birth']) $errors[] = "Row {$rowNumber}: date_of_birth must be YYYY-MM-DD.";
            $reference = mb_strtolower(trim((string) $row['external_reference']));
            if (isset($seen[$reference])) $errors[] = "Row {$rowNumber}: duplicate external_reference also used on row {$seen[$reference]}.";
            $seen[$reference] = $rowNumber;
        }
        fclose($handle);

        foreach ($errors as $error) $this->line("<error>ERROR</error> {$error}");
        if ($errors !== []) {
            $this->error(count($errors).' validation error(s); nothing was imported.');
            return self::FAILURE;
        }

        $this->info(count($seen).' member row(s) passed validation; nothing was imported.');
        return self::SUCCESS;
    }
}
