<?php

namespace Tests\Feature\Proyectos;

use App\Services\ProyectoExportService;
use Tests\TestCase;

class ProyectoExportTest extends TestCase
{
    // ── exportExcel ───────────────────────────────────────────────────────

    public function test_guest_cannot_export_excel(): void
    {
        $this->get(route('proyectos.export.excel'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_trigger_excel_export(): void
    {
        $this->mock(ProyectoExportService::class, function ($mock) {
            $mock->shouldReceive('exportarExcelTodos')
                ->once()
                ->andReturn(response()->streamDownload(fn () => print('data'), 'export.xlsx'));
        });

        $this->actingAsUser()->get(route('proyectos.export.excel'))->assertOk();
    }

    // ── exportPdf ─────────────────────────────────────────────────────────

    public function test_guest_cannot_export_pdf(): void
    {
        $this->get(route('proyectos.export.pdf'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_trigger_pdf_export(): void
    {
        $this->mock(ProyectoExportService::class, function ($mock) {
            $mock->shouldReceive('exportarPdfTodos')
                ->once()
                ->andReturn(response()->make('pdf-content', 200, ['Content-Type' => 'application/pdf']));
        });

        $this->actingAsUser()->get(route('proyectos.export.pdf'))->assertOk();
    }

    // ── exportWord ────────────────────────────────────────────────────────

    public function test_guest_cannot_export_word(): void
    {
        $this->get(route('proyectos.export.word'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_trigger_word_export(): void
    {
        $this->mock(ProyectoExportService::class, function ($mock) {
            $mock->shouldReceive('exportarWordTodos')
                ->once()
                ->andReturn(response()->streamDownload(fn () => print('data'), 'export.docx'));
        });

        $this->actingAsUser()->get(route('proyectos.export.word'))->assertOk();
    }

    // ── Export por ID (single project) ────────────────────────────────────

    public function test_export_excel_single_project_by_id(): void
    {
        $proyecto = \App\Models\Proyecto::create(['nombre_del_proyecto' => 'Test Export']);

        $this->mock(ProyectoExportService::class, function ($mock) {
            $mock->shouldReceive('exportarExcelUnico')
                ->once()
                ->andReturn(response()->streamDownload(fn () => print('xlsx'), 'proyecto.xlsx'));
        });

        $this->actingAsUser()->get(route('proyectos.export.excel', ['id' => $proyecto->id]))->assertOk();
    }

    public function test_export_pdf_single_project_by_id(): void
    {
        $proyecto = \App\Models\Proyecto::create(['nombre_del_proyecto' => 'Test Export PDF']);

        $this->mock(ProyectoExportService::class, function ($mock) {
            $mock->shouldReceive('exportarPdfUnico')
                ->once()
                ->andReturn(response()->make('pdf', 200, ['Content-Type' => 'application/pdf']));
        });

        $this->actingAsUser()->get(route('proyectos.export.pdf', ['id' => $proyecto->id]))->assertOk();
    }

    public function test_export_word_single_project_by_id(): void
    {
        $proyecto = \App\Models\Proyecto::create(['nombre_del_proyecto' => 'Test Export Word']);

        $this->mock(ProyectoExportService::class, function ($mock) {
            $mock->shouldReceive('exportarWordUnico')
                ->once()
                ->andReturn(response()->streamDownload(fn () => print('docx'), 'proyecto.docx'));
        });

        $this->actingAsUser()->get(route('proyectos.export.word', ['id' => $proyecto->id]))->assertOk();
    }

    public function test_export_excel_redirects_with_error_for_nonexistent_project(): void
    {
        // Controller catches ModelNotFoundException and redirects with error
        $this->actingAsUser()->get(route('proyectos.export.excel', ['id' => 99999]))->assertRedirect();
    }

    public function test_export_pdf_redirects_with_error_for_nonexistent_project(): void
    {
        $this->actingAsUser()->get(route('proyectos.export.pdf', ['id' => 99999]))->assertRedirect();
    }

    public function test_export_word_redirects_with_error_for_nonexistent_project(): void
    {
        $this->actingAsUser()->get(route('proyectos.export.word', ['id' => 99999]))->assertRedirect();
    }
}
