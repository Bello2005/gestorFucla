<?php

namespace App\Services;

use App\Models\Proyecto;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use Symfony\Component\HttpFoundation\Response;

class ProyectoExportService
{
    // =====================================================================
    //  PDF
    // =====================================================================

    public function exportarPdfUnico(Proyecto $proyecto): Response
    {
        $pdf = Pdf::loadView('proyectos.exports.pdf-single', compact('proyecto'));
        $filename = "Proyecto_{$proyecto->id}_" . date('Y-m-d') . '.pdf';

        return $pdf->download($filename)->header('Content-Type', 'application/pdf');
    }

    public function exportarPdfTodos(): Response
    {
        $proyectos = Proyecto::all();
        $pdf = Pdf::loadView('proyectos.exports.pdf', compact('proyectos'));

        return $pdf->download('proyectos.pdf')->header('Content-Type', 'application/pdf');
    }

    // =====================================================================
    //  Excel
    // =====================================================================

    public function exportarExcelUnico(Proyecto $proyecto): Response
    {
        [$spreadsheet, $row] = $this->crearSpreadsheetBase();
        $sheet = $spreadsheet->getActiveSheet();

        $this->escribirProyectoEnExcel($sheet, $proyecto, $row);
        $filename = "Proyecto_{$proyecto->id}_" . date('Y-m-d') . '.xlsx';

        return $this->descargarExcel($spreadsheet, $filename);
    }

    public function exportarExcelTodos(): Response
    {
        [$spreadsheet, $row] = $this->crearSpreadsheetBase();
        $sheet = $spreadsheet->getActiveSheet();

        foreach (Proyecto::all() as $proyecto) {
            $this->escribirProyectoEnExcel($sheet, $proyecto, $row);
            $row++;
        }

        return $this->descargarExcel($spreadsheet, 'proyectos_' . date('Y-m-d') . '.xlsx');
    }

    private function crearSpreadsheetBase(): array
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->mergeCells('C3:I3');
        $sheet->setCellValue('C3', 'DIRECCIÓN DE EXTENSIÓN');
        $sheet->getStyle('C3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('C4:I4');
        $sheet->setCellValue('C4', 'CONSOLIDADO DE PROYECTOS');
        $sheet->getStyle('C4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $headers = [
            'LINEAS DE ACCIÓN',
            'COBERTURA',
            'ENTIDAD CONTRATANTE',
            'FECHA DE EJECUCIÓN',
            'PLAZO',
            'VALOR TOTAL ()',
            'CARGAR ARCHIVO (PROYECTO)',
            'CARGAR CONTRATO O CONVENIO',
            'CARGAR EVIDENCIAS (FOTOGRAFIAS, VIDEOS, PIEZAS PUBLICITARIAS...)',
        ];

        $headerStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '90EE90'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ];

        foreach ($headers as $i => $header) {
            $col = Coordinate::stringFromColumnIndex($i + 1);
            $sheet->setCellValue($col . '5', $header);
            $sheet->getColumnDimension($col)->setWidth(25);
        }

        $lastCol = Coordinate::stringFromColumnIndex(count($headers));
        $sheet->getStyle("A5:{$lastCol}5")->applyFromArray($headerStyle);
        $sheet->getRowDimension(5)->setRowHeight(60);

        return [$spreadsheet, 6];
    }

    private function escribirProyectoEnExcel($sheet, Proyecto $proyecto, int $row): void
    {
        $datos = [
            $proyecto->lineas_de_accion ?? '',
            $proyecto->cobertura ?? '',
            $proyecto->entidad_contratante ?? '',
            $proyecto->fecha_de_ejecucion ? date('d/m/Y', strtotime($proyecto->fecha_de_ejecucion)) : '',
            $proyecto->plazo ?? '',
            number_format($proyecto->valor_total ?? 0, 2, ',', '.'),
            $proyecto->cargar_archivo_proyecto ? 'Sí' : 'No',
            $proyecto->cargar_contrato_o_convenio ? 'Sí' : 'No',
            $proyecto->cargar_evidencias ? count($proyecto->cargar_evidencias) . ' archivo(s)' : 'No',
        ];

        foreach ($datos as $i => $valor) {
            $col = Coordinate::stringFromColumnIndex($i + 1);
            $sheet->setCellValue($col . $row, $valor);

            $style = $sheet->getStyle($col . $row);
            $style->getAlignment()
                ->setWrapText(true)
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
            $style->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }

        $sheet->getRowDimension($row)->setRowHeight(30);
    }

    private function descargarExcel(Spreadsheet $spreadsheet, string $filename): Response
    {
        $writer   = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'excel_');
        $writer->save($tempFile);

        return response()->download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    // =====================================================================
    //  Word
    // =====================================================================

    public function exportarWordUnico(Proyecto $proyecto): Response
    {
        $phpWord = new PhpWord;
        $section = $phpWord->addSection();

        $this->escribirProyectoEnWord($section, $proyecto);
        $filename = "Proyecto_{$proyecto->id}_" . date('Y-m-d') . '.docx';

        return $this->descargarWord($phpWord, $filename);
    }

    public function exportarWordTodos(): Response
    {
        $phpWord = new PhpWord;
        $section = $phpWord->addSection();

        foreach (Proyecto::all() as $i => $proyecto) {
            if ($i > 0) {
                $section->addPageBreak();
            }
            $this->escribirProyectoEnWord($section, $proyecto);
        }

        return $this->descargarWord($phpWord, 'proyectos.docx');
    }

    private function escribirProyectoEnWord($section, Proyecto $proyecto): void
    {
        $section->addText($proyecto->nombre_del_proyecto, ['bold' => true, 'size' => 16]);
        $section->addTextBreak();

        $section->addText('ID: ' . $proyecto->id);
        $section->addText('Estado: ' . ucfirst($proyecto->estado));
        $section->addText('Valor Total: $' . number_format($proyecto->valor_total, 0, ',', '.'));
        $section->addText('Plazo: ' . $proyecto->plazo . ' meses');
        $section->addText('Fecha de Ejecución: ' . ($proyecto->fecha_de_ejecucion
            ? date('d/m/Y', strtotime($proyecto->fecha_de_ejecucion))
            : 'No especificada'));
        $section->addTextBreak();

        $section->addText('Entidad Contratante:', ['bold' => true]);
        $section->addText($proyecto->entidad_contratante);
        $section->addTextBreak();

        $section->addText('Cobertura:', ['bold' => true]);
        $section->addText($proyecto->cobertura);
        $section->addTextBreak();

        $section->addText('Objeto Contractual:', ['bold' => true]);
        $section->addText($proyecto->objeto_contractual);
        $section->addTextBreak();

        $section->addText('Líneas de Acción:', ['bold' => true]);
        $section->addText($proyecto->lineas_de_accion);
        $section->addTextBreak();

        $section->addText('Creado: ' . $proyecto->created_at->format('d/m/Y H:i:s'));
        $section->addText('Última actualización: ' . $proyecto->updated_at->format('d/m/Y H:i:s'));
    }

    private function descargarWord(PhpWord $phpWord, string $filename): Response
    {
        $tempFile     = tempnam(sys_get_temp_dir(), 'word_') . '.docx';
        $objWriter    = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($tempFile);

        $content = file_get_contents($tempFile);
        unlink($tempFile);

        return response($content)->withHeaders([
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Content-Length'      => strlen($content),
            'Cache-Control'       => 'private, no-transform, no-store, must-revalidate',
        ]);
    }
}
