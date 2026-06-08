<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\Stream;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements ToModel, WithHeadingRow
{
    protected $schoolId, $formId;
    protected $streamCache = [];
    protected $isFirstRowChecked = false;
    
    public function __construct($schoolId, $formId)
    {
        $this->schoolId = $schoolId;
        $this->formId = $formId;
    }

    public function model(array $row)
    {
        // 1. Structural Validation Guard
        if (!$this->isFirstRowChecked) {
            $this->validateIncomingHeaders($row);
            $this->isFirstRowChecked = true;
        }

        // 2. Map Columns (Handling variations like 'adm' or 'admno')
        $streamRawName = $row['stream'] ?? null;
        $studentName   = $row['name'] ?? null;
        $admNo         = $row['adm'] ?? $row['admno'] ?? null;
        $indexNo       = $row['index'] ?? null;

        // Skip empty lines
        if (!$studentName && !$admNo) return null;

        // 3. Resolve Stream
        $streamId = $this->resolveStreamId(trim($streamRawName));

        // 4. Gender Normalization
        $rawGender = strtoupper(trim($row['gender'] ?? ''));
        $gender = (str_starts_with($rawGender, 'F')) ? 'F' : 'M';

        // 5. Return Model
        return new Student([
            'school_id'        => $this->schoolId,
            'form_id'          => $this->formId,
            'stream_id'        => $streamId,
            'name'             => trim($studentName),
            'admission_number' => $admNo ? trim($admNo) : null,
            'index_number'     => $indexNo ? trim($indexNo) : null,
            'gender'           => $gender,
        ]);
    }

    protected function validateIncomingHeaders(array $row)
    {
        $keys = array_keys($row);
        // Ensure required columns exist
        if (!in_array('name', $keys) || !(in_array('adm', $keys) || in_array('admno', $keys)) || !in_array('stream', $keys)) {
            throw new \InvalidArgumentException(
                "Template Validation Error: Your Excel file must include columns named: 'name', 'adm' (or 'admno'), and 'stream'."
            );
        }
    }

    private function resolveStreamId($streamName)
    {
        $cacheKey = Str::slug($streamName);
        if (isset($this->streamCache[$cacheKey])) return $this->streamCache[$cacheKey];

        $stream = Stream::firstOrCreate([
            'school_id' => $this->schoolId,
            'form_id'   => $this->formId,
            'name'      => $streamName
        ]);

        $this->streamCache[$cacheKey] = $stream->id;
        return $stream->id;
    }
}