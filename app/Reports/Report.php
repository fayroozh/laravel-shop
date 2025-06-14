<?php

namespace App\Reports;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

abstract class Report
{
    protected $startDate;
    protected $endDate;
    protected $filters = [];
    
    public function __construct($startDate = null, $endDate = null, $filters = [])
    {
        $this->startDate = $startDate ? Carbon::parse($startDate) : Carbon::now()->startOfMonth();
        $this->endDate = $endDate ? Carbon::parse($endDate) : Carbon::now();
        $this->filters = $filters;
    }
    
    abstract public function generate();
    
    public function toArray()
    {
        return [
            'data' => $this->generate(),
            'meta' => [
                'start_date' => $this->startDate->format('Y-m-d'),
                'end_date' => $this->endDate->format('Y-m-d'),
                'generated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ]
        ];
    }
}