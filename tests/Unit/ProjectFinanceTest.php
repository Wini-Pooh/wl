<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\Project;
use App\Models\Estimate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectFinanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_project_costs_update_from_estimates()
    {
        // Создаем партнера
        $partner = User::factory()->create(['role' => 'partner']);
        
        // Создаем проект
        $project = Project::factory()->create([
            'partner_id' => $partner->id,
            'work_cost' => 0,
            'materials_cost' => 0,
            'additional_work_cost' => 0,
            'total_cost' => 0
        ]);

        // Создаем смету основных работ
        $mainEstimate = Estimate::create([
            'project_id' => $project->id,
            'name' => 'Основные работы',
            'type' => 'main',
            'status' => 'in_progress',
            'created_by' => $partner->id,
            'data' => [
                'totals' => [
                    'client_total' => 100000
                ]
            ],
            'total_amount' => 100000
        ]);

        // Создаем смету материалов
        $materialsEstimate = Estimate::create([
            'project_id' => $project->id,
            'name' => 'Материалы',
            'type' => 'materials',
            'status' => 'in_progress',
            'created_by' => $partner->id,
            'data' => [
                'totals' => [
                    'client_total' => 50000
                ]
            ],
            'total_amount' => 50000
        ]);

        // Создаем смету дополнительных работ
        $additionalEstimate = Estimate::create([
            'project_id' => $project->id,
            'name' => 'Дополнительные работы',
            'type' => 'additional',
            'status' => 'in_progress',
            'created_by' => $partner->id,
            'data' => [
                'totals' => [
                    'client_total' => 30000
                ]
            ],
            'total_amount' => 30000
        ]);

        // Обновляем финансовые показатели проекта
        $project->updateCostsFromEstimates();

        // Проверяем результат
        $this->assertEquals(100000, $project->work_cost);
        $this->assertEquals(50000, $project->materials_cost);
        $this->assertEquals(30000, $project->additional_work_cost);
        $this->assertEquals(180000, $project->total_cost);
    }

    public function test_project_costs_update_with_multiple_estimates_same_type()
    {
        // Создаем партнера
        $partner = User::factory()->create(['role' => 'partner']);
        
        // Создаем проект
        $project = Project::factory()->create([
            'partner_id' => $partner->id,
            'work_cost' => 0,
            'materials_cost' => 0,
            'additional_work_cost' => 0,
            'total_cost' => 0
        ]);

        // Создаем две сметы основных работ
        Estimate::create([
            'project_id' => $project->id,
            'name' => 'Основные работы - этап 1',
            'type' => 'main',
            'status' => 'in_progress',
            'created_by' => $partner->id,
            'data' => [
                'totals' => [
                    'client_total' => 60000
                ]
            ],
            'total_amount' => 60000
        ]);

        Estimate::create([
            'project_id' => $project->id,
            'name' => 'Основные работы - этап 2',
            'type' => 'main',
            'status' => 'in_progress',
            'created_by' => $partner->id,
            'data' => [
                'totals' => [
                    'client_total' => 40000
                ]
            ],
            'total_amount' => 40000
        ]);

        // Обновляем финансовые показатели проекта
        $project->updateCostsFromEstimates();

        // Проверяем, что суммы сложились
        $this->assertEquals(100000, $project->work_cost);
        $this->assertEquals(0, $project->materials_cost);
        $this->assertEquals(0, $project->additional_work_cost);
        $this->assertEquals(100000, $project->total_cost);
    }
}
