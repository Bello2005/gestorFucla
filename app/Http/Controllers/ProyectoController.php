<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;

class ProyectoController extends Controller
{
    public function exportPdf(Request $request)
    {
        try {
            $proyecto = null;

            if ($request->has('id')) {
                $proyecto = Proyecto::findOrFail($request->id);
                $pdf = Pdf::loadView('proyectos.exports.pdf-single', ['proyecto' => $proyecto]);
                $nombreArchivo = 'Proyecto_'.$proyecto->id.'_'.date('Y-m-d').'.pdf';

                return $pdf->download($nombreArchivo)->header('Content-Type', 'application/pdf');
            }

            $proyectos = Proyecto::all();
            $pdf = Pdf::loadView('proyectos.exports.pdf', ['proyectos' => $proyectos]);

            return $pdf->download('proyectos.pdf')->header('Content-Type', 'application/pdf');
        } catch (\Exception $e) {
            Log::error('Error al exportar a PDF', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            if ($request->ajax()) {
                return response()->json(['error' => 'Error al exportar a PDF: '.$e->getMessage()], 500);
            }

            return redirect()->back()->withErrors(['error' => 'Error al exportar a PDF: '.$e->getMessage()]);
        }
    }

    public function exportExcel(Request $request)
    {
        try {
            $spreadsheet = new Spreadsheet;
            $sheet = $spreadsheet->getActiveSheet();

            // Configurar el título
            $sheet->mergeCells('C3:I3');
            $sheet->setCellValue('C3', 'DIRECCIÓN DE EXTENSIÓN');
            $sheet->getStyle('C3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->mergeCells('C4:I4');
            $sheet->setCellValue('C4', 'CONSOLIDADO DE PROYECTOS');
            $sheet->getStyle('C4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Establecer encabezados
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

            // Establecer el estilo para los encabezados
            $headerStyle = [
                'font' => [
                    'bold' => true,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '90EE90'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ];

            // Aplicar encabezados y estilos
            foreach ($headers as $key => $header) {
                $col = Coordinate::stringFromColumnIndex($key + 1);
                $sheet->setCellValue($col.'5', $header);
                $sheet->getColumnDimension($col)->setWidth(25);
            }

            // Aplicar estilo a toda la fila de encabezados
            $lastCol = Coordinate::stringFromColumnIndex(count($headers));
            $sheet->getStyle('A5:'.$lastCol.'5')->applyFromArray($headerStyle);

            // Ajustar altura de la fila de encabezados
            $sheet->getRowDimension(5)->setRowHeight(60);

            // Comenzar desde la fila 6 para los datos
            $row = 6;
            if ($request->has('id')) {
                $proyecto = Proyecto::findOrFail($request->id);
                $this->writeProyectoToExcel($sheet, $proyecto, $row);
                $filename = 'Proyecto_'.$proyecto->id.'_'.date('Y-m-d').'.xlsx';
            } else {
                $proyectos = Proyecto::all();
                foreach ($proyectos as $proyecto) {
                    $this->writeProyectoToExcel($sheet, $proyecto, $row);
                    $row++;
                }
                $filename = 'proyectos_'.date('Y-m-d').'.xlsx';
            }

            // Crear archivo Excel
            $writer = new Xlsx($spreadsheet);
            $tempFile = tempnam(sys_get_temp_dir(), 'excel_');
            $writer->save($tempFile);

            return response()->download($tempFile, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Error al exportar a Excel', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->withErrors(['error' => 'Error al exportar a Excel: '.$e->getMessage()]);
        }
    }

    private function writeProyectoToExcel($sheet, $proyecto, $row)
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
            $proyecto->cargar_evidencias ? count($proyecto->cargar_evidencias).' archivo(s)' : 'No',
        ];

        foreach ($datos as $key => $valor) {
            $colIndex = $key + 1;
            $col = Coordinate::stringFromColumnIndex($colIndex);
            $sheet->setCellValue($col.$row, $valor);

            // Aplicar estilo a la celda
            $cellStyle = $sheet->getStyle($col.$row);
            $cellStyle->getAlignment()
                ->setWrapText(true)
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);

            $cellStyle->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }

        // Ajustar altura de la fila
        $sheet->getRowDimension($row)->setRowHeight(30);
    }

    public function exportWord(Request $request)
    {
        try {
            $phpWord = new PhpWord;
            $section = $phpWord->addSection();

            if ($request->has('id')) {
                $proyecto = Proyecto::findOrFail($request->id);
                $this->writeProyectoToWord($section, $proyecto);
                $filename = 'Proyecto_'.$proyecto->id.'_'.date('Y-m-d').'.docx';
            } else {
                $proyectos = Proyecto::all();
                foreach ($proyectos as $proyecto) {
                    $this->writeProyectoToWord($section, $proyecto);
                    $section->addPageBreak();
                }
                $filename = 'proyectos.docx';
            }

            // Crear archivo temporal con extensión .docx
            $tempFile = tempnam(sys_get_temp_dir(), 'word_');
            $finalTempFile = $tempFile.'.docx';
            rename($tempFile, $finalTempFile);

            // Guardar el documento
            $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
            $objWriter->save($finalTempFile);

            // Leer el contenido y eliminar el archivo temporal
            $content = file_get_contents($finalTempFile);
            unlink($finalTempFile);

            // Devolver la respuesta con los headers correctos
            return response($content)
                ->withHeaders([
                    'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'Content-Disposition' => 'attachment; filename="'.$filename.'"',
                    'Content-Length' => strlen($content),
                    'Cache-Control' => 'private, no-transform, no-store, must-revalidate',
                ]);
        } catch (\Exception $e) {
            Log::error('Error al exportar a Word', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->withErrors(['error' => 'Error al exportar a Word: '.$e->getMessage()]);
        }
    }

    private function writeProyectoToWord($section, $proyecto)
    {
        // Título del proyecto
        $section->addText($proyecto->nombre_del_proyecto, ['bold' => true, 'size' => 16]);
        $section->addTextBreak();

        // Información básica
        $section->addText('ID: '.$proyecto->id);
        $section->addText('Estado: '.ucfirst($proyecto->estado));
        $section->addText('Valor Total: $'.number_format($proyecto->valor_total, 0, ',', '.'));
        $section->addText('Plazo: '.$proyecto->plazo.' meses');
        $section->addText('Fecha de Ejecución: '.($proyecto->fecha_de_ejecucion ? date('d/m/Y', strtotime($proyecto->fecha_de_ejecucion)) : 'No especificada'));
        $section->addTextBreak();

        // Información detallada
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

        // Fechas
        $section->addText('Creado: '.$proyecto->created_at->format('d/m/Y H:i:s'));
        $section->addText('Última actualización: '.$proyecto->updated_at->format('d/m/Y H:i:s'));
    }

    public function index()
    {
        $order = request('order', 'desc');
        $q = Proyecto::query();
        if (request('cert') === 'con') {
            $q->whereNotNull('certificado_cumplimiento');
        } elseif (request('cert') === 'sin') {
            $q->whereNull('certificado_cumplimiento');
        }
        $proyectos = $q->orderBy('created_at', $order)->get();

        $entidades = Proyecto::distinct()
            ->whereNotNull('entidad_contratante')
            ->pluck('entidad_contratante')
            ->unique()
            ->values()
            ->toArray();

        return view('proyectos.index', compact('proyectos', 'entidades'));
    }

    public function create()
    {
        return view('proyectos.create');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'nombre_del_proyecto' => 'required|string|max:255',
                'objeto_contractual' => 'nullable|string|max:255',
                'lineas_de_accion' => 'nullable|string',
                'cobertura' => 'nullable|string|max:255',
                'entidad_contratante' => 'nullable|string|max:255',
                'fecha_de_ejecucion' => 'nullable|date',
                'plazo' => 'nullable|numeric|min:0',
                'valor_total' => 'nullable|numeric|min:0',
            ]);

            $data = $validated;

            if ($request->hasFile('archivo_proyecto')) {
                $data['cargar_archivo_proyecto'] = $request->file('archivo_proyecto')
                    ->store('proyectos/archivos', 'public');
            }

            if ($request->hasFile('archivo_contrato')) {
                $data['cargar_contrato_o_convenio'] = $request->file('archivo_contrato')
                    ->store('proyectos/contratos', 'public');
            }

            if ($request->hasFile('evidencias')) {
                $evidencias = [];
                foreach ($request->file('evidencias') as $evidencia) {
                    $evidencias[] = $evidencia->store('proyectos/evidencias', 'public');
                }
                $data['cargar_evidencias'] = $evidencias;
            }

            $proyecto = Proyecto::create($data);
            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Proyecto creado exitosamente',
                    'id' => $proyecto->id,
                ]);
            }

            return redirect()
                ->route('proyectos.index')
                ->with('success', 'Proyecto creado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear proyecto', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Error al crear el proyecto: '.$e->getMessage(),
                ], 500);
            }

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Error al crear el proyecto: '.$e->getMessage()]);
        }
    }

    public function show(Proyecto $proyecto)
    {
        return view('proyectos.show', compact('proyecto'));
    }

    public function edit(Proyecto $proyecto)
    {
        return view('proyectos.edit', compact('proyecto'));
    }

    public function update(Request $request, Proyecto $proyecto)
    {
        DB::beginTransaction();
        try {
            Log::info('Iniciando actualización', [
                'proyecto_id' => $proyecto->id,
                'request_data' => $request->all(),
            ]);

            $securityChecks = [
                'method' => $request->input('_method') === 'PUT',
                'is_edit' => $request->input('is_edit') === '1',
                'project_id' => $request->input('proyecto_id') == $proyecto->id,
            ];

            $failedChecks = array_filter($securityChecks, fn ($check) => ! $check);

            if (! empty($failedChecks)) {
                Log::error('Falló la validación de seguridad', [
                    'proyecto_id' => $proyecto->id,
                    'failed_checks' => array_keys($failedChecks),
                    'request_data' => $request->all(),
                ]);
                throw new \Exception('Falló la validación de seguridad');
            }

            $validated = $request->validate([
                'nombre_del_proyecto' => 'required|string|max:255',
                'objeto_contractual' => 'nullable|string|max:255',
                'lineas_de_accion' => 'nullable|string',
                'cobertura' => 'nullable|string|max:255',
                'entidad_contratante' => 'nullable|string|max:255',
                'fecha_de_ejecucion' => 'nullable|date',
                'plazo' => 'nullable|numeric|min:0',
                'valor_total' => 'nullable|numeric|min:0',
                'estado' => 'nullable|string|in:activo,inactivo,cerrado',
            ]);

            if ($request->hasFile('archivo_proyecto')) {
                if ($proyecto->cargar_archivo_proyecto) {
                    Storage::disk('public')->delete($proyecto->cargar_archivo_proyecto);
                }
                $validated['cargar_archivo_proyecto'] = $request->file('archivo_proyecto')
                    ->store('proyectos/archivos', 'public');
            }

            if ($request->hasFile('archivo_contrato')) {
                if ($proyecto->cargar_contrato_o_convenio) {
                    Storage::disk('public')->delete($proyecto->cargar_contrato_o_convenio);
                }
                $validated['cargar_contrato_o_convenio'] = $request->file('archivo_contrato')
                    ->store('proyectos/contratos', 'public');
            }

            if ($request->hasFile('evidencias')) {
                $evidencias = $proyecto->cargar_evidencias ?? [];
                foreach ($request->file('evidencias') as $evidencia) {
                    $evidencias[] = $evidencia->store('proyectos/evidencias', 'public');
                }
                $validated['cargar_evidencias'] = $evidencias;
            }

            $proyecto->update($validated);
            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Proyecto actualizado exitosamente',
                ]);
            }

            return redirect()
                ->route('proyectos.show', $proyecto)
                ->with('success', 'Proyecto actualizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar proyecto', [
                'proyecto_id' => $proyecto->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Error al actualizar el proyecto: '.$e->getMessage(),
                ], 500);
            }

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Error al actualizar el proyecto: '.$e->getMessage()]);
        }
    }

    public function destroy(Request $request, Proyecto $proyecto)
    {
        try {
            if ($proyecto->cargar_archivo_proyecto) {
                Storage::disk('public')->delete($proyecto->cargar_archivo_proyecto);
            }

            if ($proyecto->cargar_contrato_o_convenio) {
                Storage::disk('public')->delete($proyecto->cargar_contrato_o_convenio);
            }

            if ($proyecto->cargar_evidencias) {
                foreach ($proyecto->cargar_evidencias as $evidencia) {
                    Storage::disk('public')->delete($evidencia);
                }
            }

            $proyecto->delete();

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Proyecto eliminado exitosamente',
                ]);
            }

            return redirect()
                ->route('proyectos.index')
                ->with('success', 'Proyecto eliminado exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error al eliminar proyecto', [
                'proyecto_id' => $proyecto->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Error al eliminar el proyecto: '.$e->getMessage(),
                ], 500);
            }

            return redirect()
                ->back()
                ->withErrors(['error' => 'Error al eliminar el proyecto: '.$e->getMessage()]);
        }
    }

    public function deleteEvidenciaArchivo(Proyecto $proyecto, $indice)
    {
        try {
            $evidencias = is_array($proyecto->cargar_evidencias) ? $proyecto->cargar_evidencias : [];
            if (isset($evidencias[$indice])) {
                $archivo = $evidencias[$indice];
                Storage::disk('public')->delete($archivo);
                unset($evidencias[$indice]);
                $proyecto->cargar_evidencias = array_values($evidencias);
                $proyecto->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Evidencia eliminada exitosamente.',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No se encontró la evidencia para eliminar.',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error al eliminar evidencia', [
                'proyecto_id' => $proyecto->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la evidencia: '.$e->getMessage(),
            ], 500);
        }
    }

    public function deleteContratoArchivo(Proyecto $proyecto)
    {
        try {
            if ($proyecto->cargar_contrato_o_convenio) {
                Storage::disk('public')->delete($proyecto->cargar_contrato_o_convenio);
                $proyecto->cargar_contrato_o_convenio = null;
                $proyecto->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Archivo de contrato/convenio eliminado exitosamente.',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No se encontró el archivo de contrato/convenio para eliminar.',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error al eliminar archivo de contrato/convenio', [
                'proyecto_id' => $proyecto->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el archivo de contrato/convenio: '.$e->getMessage(),
            ], 500);
        }
    }

    public function deleteProyectoArchivo(Proyecto $proyecto)
    {
        try {
            if ($proyecto->cargar_archivo_proyecto) {
                Storage::disk('public')->delete($proyecto->cargar_archivo_proyecto);
                $proyecto->cargar_archivo_proyecto = null;
                $proyecto->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Archivo del proyecto eliminado exitosamente.',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No se encontró el archivo para eliminar.',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error al eliminar archivo del proyecto', [
                'proyecto_id' => $proyecto->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el archivo: '.$e->getMessage(),
            ], 500);
        }
    }

    public function subirCertificado(Request $request, Proyecto $proyecto)
    {
        $request->validate([
            'certificado' => 'required|file|mimes:pdf|max:10240',
            'certificado_fecha' => 'nullable|date',
            'certificado_observaciones' => 'nullable|string',
        ]);
        $path = $request->file('certificado')->store('proyectos/certificados/'.$proyecto->id, 'public');
        if ($proyecto->certificado_cumplimiento) {
            Storage::disk('public')->delete($proyecto->certificado_cumplimiento);
        }
        $proyecto->update([
            'certificado_cumplimiento' => $path,
            'certificado_fecha' => $request->certificado_fecha,
            'certificado_observaciones' => $request->certificado_observaciones,
        ]);

        return redirect()->back()->with('success', 'Certificado de cumplimiento guardado.');
    }

    public function eliminarCertificado(Proyecto $proyecto)
    {
        if ($proyecto->certificado_cumplimiento) {
            Storage::disk('public')->delete($proyecto->certificado_cumplimiento);
        }
        $proyecto->update([
            'certificado_cumplimiento' => null,
            'certificado_fecha' => null,
            'certificado_observaciones' => null,
        ]);

        return redirect()->back()->with('success', 'Certificado eliminado.');
    }
}
