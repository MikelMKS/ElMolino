<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use GrahamCampbell\Flysystem\Facades\Flysystem;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use DB;

class VentasSemanalController extends Controller
{
    public function index(){
        return view('cargaVentaSemanal');
    }

    protected function generarEstiloCelda($opciones = [], $contabilidad = false, $soloBordesExternos = false)
    {
        // Valores por defecto
        $defaultOptions = [
            'bold' => true,
            'size' => 24,
            'color' => 'FF000000',
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,
            'argb' => 'FF000000', // Color negro por defecto
        ];

        // Fusionar opciones proporcionadas con valores por defecto
        $options = array_merge($defaultOptions, $opciones);

        // Estilo de los bordes
        // $bordersStyle = $soloBordesExternos ? Border::BORDER_NONE : Border::BORDER_THIN;

        // Crear el array de estilo
        $styleArray = [
            'font' => [
                'bold' => $options['bold'],
                'size' => $options['size'],
                'color' => ['argb' => $options['color']],
            ],
            'alignment' => [
                'horizontal' => $options['horizontal'],
                'vertical' => $options['vertical'],
            ],
            'borders' => [
                $soloBordesExternos ? 'outline' : 'allBorders' => [
                    'borderStyle' => Border::BORDER_THICK,
                ],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => $options['argb']],
            ],
        ];

        if ($contabilidad) {
            $styleArray['numberFormat'] = [
                'formatCode' => '#,##0.00' . ';-0;;@'
            ];
        }

        if($soloBordesExternos){
            $styleArray['borders']['inside'] = [
                    'borderStyle' => Border::BORDER_THIN,
            ];
        }

        return $styleArray;
    }

    public function cargaLayoutVentaSemanal(Request $request){
        $response = array('status' => 0,'msg' => ''); 
    
        $file = $request->file('layout');
        $tabla = array();
        $ticket = null;
    
        if($file == null){
            $response['status'] = '1';
            $response['msg'] = 'SELECCIONA UN ARCHIVO';
        } else {
            try {
                $path = $request->file('layout')->getRealPath();
                // Cargar la hoja de Excel
                $spreadsheet = IOFactory::load($path);
                $sheet = $spreadsheet->getActiveSheet();
    
                // Obtener la matriz de celdas
                $data = [];
                foreach ($sheet->getRowIterator() as $row) {
                    $rowData = [];
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false); // Incluye celdas vacías
    
                    foreach ($cellIterator as $cell) {
                        $cellValue = $cell->getValue();
                        $columnName = $cell->getColumn();
                        $rowData[$columnName] = $cellValue;
                    }
    
                    $data[] = $rowData;
                }
    
                $foundTicket = false;
    
                foreach($data as $key => $value){
                    // Verificar si el valor de 'F' es 'ticket' y no se ha encontrado antes
                    if ($value['A'] === 'Ticket' && !$foundTicket) {
                        $foundTicket = true; // Marcamos que hemos encontrado 'ticket'
                    }
                    if ($value['A'] === null) {
                        $foundTicket = false;
                    }
                    if ($foundTicket) {
                        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        if(!empty($value['F'])){
                            if($ticket != null){
                                array_push($tabla,$ticket);
                                $ticket = null;
                            }
                            $folio = $value['F'];
                            $nombre = $value['H'];
                            $ticket = array('folio' => $folio, 'nombre' => $nombre);
                        }else{
                            $producto = $value['E'];
                            $cantidad = str_replace(str_split(' ,'), '', $value['B']);
                            $precio = str_replace(str_split('$ ,'), '', $value['S']);
    
                            $grupoBusca = DB::select("SELECT idGrupo FROM productos AS p
                            LEFT JOIN grupos_productos AS gp ON p.id = gp.idProducto
                            WHERE p.nombre LIKE '$producto'");
                            if($grupoBusca != null){
                                $grupo = $grupoBusca[0]->idGrupo;
                            }else{
                                $response['status'] = '1';
                                $response['msg'] = 'EL PRODUCTO "'.$producto.'" DE LA FILA #'.($key + 1).' NO EXISTE EN LA BASE DE DATOS, DEBES DARLO DE ALTA';
                                break;
                            }
                            if(in_array('g'.$grupo,$ticket)){
                                array_push($tabla,$ticket);
                                $ticket = array('folio' => $folio, 'nombre' => $nombre);
                            }
    
                            $ticket['g'.$grupo] = 'g'.$grupo;
                            $ticket['cantidad_'.$grupo] = $cantidad;
                            $ticket['precio_'.$grupo] = $precio;
                        }
                        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                    }
                }
                array_push($tabla,$ticket);
            } catch (\Exception $e) {
                $response['status'] = '1';
                $response['msg'] = $e->getMessage();
            }
        }
    
        if($response['status'] != 1){
            echo json_encode($tabla);
        } else {
            echo json_encode($response);
        }
    }

    public function descagaLayoutSemanal(Request $request){
        $tabla = json_decode($request->input('tabla'), true);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('LISTA');
        $colorABC = '5468FF';
        $tablaTotal = count($tabla)+2;

        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $grupos = DB::select("SELECT * FROM grupos");
        ////////////////////////////////////////////////////////////////////////////
        // Titulos
        $fila1 = [
            /*A*/ 'GENERALES',
            /*B (A)*/ '',
            /*C*/ '',
        ];
        // Subtitulos
        $fila2 = [
            /*A*/ 'FOLIO',
            /*B*/ 'NOMBRE',
            /*C*/ '',
        ];
        // TOTALES 
        $filaTotales = [
            /*A*/ 'TOTALES',
            /*B (A)*/ '',
            /*C*/ '',
        ];
        
        $letra = 'D';
        foreach($grupos as $g){

            array_push($fila1,$g->nombre,'','');
            array_push($fila2,$g->encabezado1,$g->encabezado2,$g->encabezado3);
            array_push($filaTotales,
                '=SUM('.$letra.'3:'.$letra.$tablaTotal.')',
                '=SUM('.siguienteLetra($letra).'3:'.siguienteLetra($letra).$tablaTotal.')',
                '=SUM('.siguienteLetra(siguienteLetra($letra)).'3:'.siguienteLetra(siguienteLetra($letra)).$tablaTotal.')');

            $styleArrayGrupos = $this->generarEstiloCelda(['argb' => $g->color],false);

            $rangeTit = $letra.'1:'.siguienteLetra(siguienteLetra($letra)).'1';
            $rangeSub = $letra.'2:'.siguienteLetra(siguienteLetra($letra)).'2';
            $sheet->mergeCells($rangeTit)->getStyle($rangeTit)->applyFromArray($styleArrayGrupos);
            $sheet->getStyle($rangeSub)->applyFromArray($styleArrayGrupos);


            // //////////////////////////////////////////////////////////////////////// ESTILOS SECCION DATOS 
            $styleArrayDatos = $this->generarEstiloCelda(['horizontal' => Alignment::HORIZONTAL_RIGHT,'argb' => hacerColorMasClaro($g->color),'bold' => false],true,true);
            $rangeDatos = $letra.'3:'.siguienteLetra(siguienteLetra($letra)).$tablaTotal;
            $sheet->getStyle($rangeDatos)->applyFromArray($styleArrayDatos);
            // /////////////////////////////////

            $letra = siguienteLetra(siguienteLetra(siguienteLetra($letra)));
        }

        array_push($fila1,'TOTAL PAPÁ','TOTAL FANNY','GRAN TOTAL','ABONO TOTAL','ABONO TOTAL','ABONO TOTAL','TRANSFER','TOTAL BONOS','ABONO A FANNY','ABONO A PAPÁ','RESTO','RESTOS','','');
        $sheet->fromArray($fila1);
        $styleArray = $this->generarEstiloCelda(['argb' => $colorABC]);
        
        $sheet->mergeCells('A1:B1')->getStyle('A1:B1')->applyFromArray($styleArray);
        $sheet->getStyle('C1')->applyFromArray($styleArray);

        array_push($fila2,'','','','SAB Y DOM','LUNES','VIERNES','','','','','','FANNY','PAPÁ','COMPROBACION');
        $sheet->fromArray($fila2,NULL,'A2');
        $sheet->getStyle('A2:C2')->applyFromArray($styleArray);
        //////////////////////////////////////////////////////////////////////////// ESTILOS COLUMNAS DESPUES GRUPOS
        // ----- //
        $styleArrayDG = $this->generarEstiloCelda(['argb' => '145A32'],false,true);
        $styleArrayDGDatos = $this->generarEstiloCelda(['horizontal' => Alignment::HORIZONTAL_RIGHT,'argb' => hacerColorMasClaro('145A32'),'bold' => false],true,true);

        $letraIni = $letra;
        $sheet->getStyle($letraIni.'1:'.$letraIni.'2')->applyFromArray($styleArrayDG);
        $sheet->getStyle($letraIni.'1:'.$letraIni.'2')->applyFromArray($styleArrayDG);
        $sheet->getStyle($letraIni.'3:'.$letraIni.$tablaTotal)->applyFromArray($styleArrayDGDatos);
        array_push($filaTotales,'=SUM('.$letraIni.'3:'.$letraIni.$tablaTotal.')');

        $letraIni = siguienteLetra($letraIni);
        $sheet->getStyle($letraIni.'1:'.$letraIni.'2')->applyFromArray($styleArrayDG);
        $sheet->getStyle($letraIni.'3:'.$letraIni.$tablaTotal)->applyFromArray($styleArrayDGDatos);
        array_push($filaTotales,'=SUM('.$letraIni.'3:'.$letraIni.$tablaTotal.')');

        $letraIni = siguienteLetra($letraIni);
        $sheet->getStyle($letraIni.'1:'.$letraIni.'2')->applyFromArray($styleArrayDG);
        $sheet->getStyle($letraIni.'3:'.$letraIni.$tablaTotal)->applyFromArray($styleArrayDGDatos);
        array_push($filaTotales,'=SUM('.$letraIni.'3:'.$letraIni.$tablaTotal.')');
        // ----- //
        // ----- //
        $styleArrayDG = $this->generarEstiloCelda(['argb' => 'F1C40F'],false,true);
        $styleArrayDGDatos = $this->generarEstiloCelda(['horizontal' => Alignment::HORIZONTAL_RIGHT,'argb' => hacerColorMasClaro('F1C40F'),'bold' => false],true,true);

        $letraIni = siguienteLetra($letraIni);
        $sheet->getStyle($letraIni.'1:'.$letraIni.'2')->applyFromArray($styleArrayDG);
        $sheet->getStyle($letraIni.'3:'.$letraIni.$tablaTotal)->applyFromArray($styleArrayDGDatos);
        array_push($filaTotales,'=SUM('.$letraIni.'3:'.$letraIni.$tablaTotal.')');

        $letraIni = siguienteLetra($letraIni);
        $sheet->getStyle($letraIni.'1:'.$letraIni.'2')->applyFromArray($styleArrayDG);
        $sheet->getStyle($letraIni.'3:'.$letraIni.$tablaTotal)->applyFromArray($styleArrayDGDatos);
        array_push($filaTotales,'=SUM('.$letraIni.'3:'.$letraIni.$tablaTotal.')');

        $letraIni = siguienteLetra($letraIni);
        $sheet->getStyle($letraIni.'1:'.$letraIni.'2')->applyFromArray($styleArrayDG);
        $sheet->getStyle($letraIni.'3:'.$letraIni.$tablaTotal)->applyFromArray($styleArrayDGDatos);
        array_push($filaTotales,'=SUM('.$letraIni.'3:'.$letraIni.$tablaTotal.')');

        $letraIni = siguienteLetra($letraIni);
        $sheet->getStyle($letraIni.'1:'.$letraIni.'2')->applyFromArray($styleArrayDG);
        $sheet->getStyle($letraIni.'3:'.$letraIni.$tablaTotal)->applyFromArray($styleArrayDGDatos);
        array_push($filaTotales,'=SUM('.$letraIni.'3:'.$letraIni.$tablaTotal.')');

        $letraIni = siguienteLetra($letraIni);
        $sheet->getStyle($letraIni.'1:'.$letraIni.'2')->applyFromArray($styleArrayDG);
        $sheet->getStyle($letraIni.'3:'.$letraIni.$tablaTotal)->applyFromArray($styleArrayDGDatos);
        array_push($filaTotales,'=SUM('.$letraIni.'3:'.$letraIni.$tablaTotal.')');
        // ----- //
        // ----- //
        $styleArrayDG = $this->generarEstiloCelda(['argb' => 'D317DF'],false,true);
        $styleArrayDGDatos = $this->generarEstiloCelda(['horizontal' => Alignment::HORIZONTAL_RIGHT,'argb' => hacerColorMasClaro('D317DF'),'bold' => false],true,true);

        $letraIni = siguienteLetra($letraIni);
        $sheet->getStyle($letraIni.'1:'.$letraIni.'2')->applyFromArray($styleArrayDG);
        $sheet->getStyle($letraIni.'3:'.$letraIni.$tablaTotal)->applyFromArray($styleArrayDGDatos);
        array_push($filaTotales,'=SUM('.$letraIni.'3:'.$letraIni.$tablaTotal.')');
        // ----- //
        // ----- //
        $styleArrayDG = $this->generarEstiloCelda(['argb' => '2E86C1'],false,true);
        $styleArrayDGDatos = $this->generarEstiloCelda(['horizontal' => Alignment::HORIZONTAL_RIGHT,'argb' => hacerColorMasClaro('2E86C1'),'bold' => false],true,true);

        $letraIni = siguienteLetra($letraIni);
        $sheet->getStyle($letraIni.'1:'.$letraIni.'2')->applyFromArray($styleArrayDG);
        $sheet->getStyle($letraIni.'3:'.$letraIni.$tablaTotal)->applyFromArray($styleArrayDGDatos);
        array_push($filaTotales,'=SUM('.$letraIni.'3:'.$letraIni.$tablaTotal.')');
        // ----- //
        // ----- //
        $styleArrayDG = $this->generarEstiloCelda(['argb' => 'CB4335'],false,true);
        $styleArrayDGDatos = $this->generarEstiloCelda(['horizontal' => Alignment::HORIZONTAL_RIGHT,'argb' => hacerColorMasClaro('CB4335'),'bold' => false],true,true);

        $letraIni = siguienteLetra($letraIni);
        $sheet->getStyle($letraIni.'1:'.$letraIni.'2')->applyFromArray($styleArrayDG);
        $sheet->getStyle($letraIni.'3:'.$letraIni.$tablaTotal)->applyFromArray($styleArrayDGDatos);
        array_push($filaTotales,'=SUM('.$letraIni.'3:'.$letraIni.$tablaTotal.')');
        // ----- //
        // ----- //
        $styleArrayDG = $this->generarEstiloCelda(['argb' => '65B005']);
        $styleArrayDGDatos = $this->generarEstiloCelda(['horizontal' => Alignment::HORIZONTAL_RIGHT,'argb' => hacerColorMasClaro('65B005'),'bold' => false],true,true);

        $letraIni = siguienteLetra($letraIni);
        $letraFin = siguienteLetra(siguienteLetra($letraIni));
        $sheet->mergeCells($letraIni.'1:'.$letraFin.'1')->getStyle($letraIni.'1:'.$letraFin.'1')->applyFromArray($styleArrayDG);
        $sheet->getStyle($letraIni.'2:'.$letraFin.'2')->applyFromArray($styleArrayDG);
        $sheet->getStyle($letraIni.'3:'.$letraFin.$tablaTotal)->applyFromArray($styleArrayDGDatos);

        array_push($filaTotales,'=SUM('.$letraIni.'3:'.$letraIni.$tablaTotal.')');
        array_push($filaTotales,'=SUM('.siguienteLetra($letraIni).'3:'.siguienteLetra($letraIni).$tablaTotal.')');
        array_push($filaTotales,'=SUM('.$letraFin.'3:'.$letraFin.$tablaTotal.')');
        // ----- //
        $sheet->setAutoFilter('A2:'.$letraFin.'2');
        $sheet->freezePane('D3');
        ////////////////////////////////////////////////////////////////////////////

        $row=3;

        foreach($tabla as $t){
            $filaDatos = [
                /*A*/ $t['folio'],
                /*B*/ $t['nombre'],
                /*C*/ '',
            ];

            $sumaPapa = null;
            $sumaFanny = null;

            
            $currentColumnIndex = 'F';
            foreach($grupos as $g){
                
                $previousColumnIndex1 = chr(ord($currentColumnIndex) - 1);
                $previousColumnIndex2 = chr(ord($currentColumnIndex) - 2);

                if(in_array('g'.$g->id,$t)){
                    array_push($filaDatos,$t['cantidad_'.$g->id],$t['precio_'.$g->id],'='.$previousColumnIndex1.$row.'*'.$previousColumnIndex2.$row);
                }else{
                    array_push($filaDatos,'','','='.$previousColumnIndex1.$row.'*'.$previousColumnIndex2.$row);
                }

                if($g->suma == 0) { // Suma 0 es para papa, 1 es para fanny
                    $sumaPapa == null ? $sumaPapa = $currentColumnIndex.$row : $sumaPapa.= '+'.$currentColumnIndex.$row;
                }else{
                    $sumaFanny == null ? $sumaFanny = $currentColumnIndex.$row : $sumaFanny.= '+'.$currentColumnIndex.$row;
                }

                $currentColumnIndex = siguienteLetra(siguienteLetra(siguienteLetra($currentColumnIndex)));
            }

            // Aqui es para las columnas de despues de todos los grupos
            // esta es secion 1 totales papa y fanny en olumna gran total
            $colTotalFanny = chr(ord($currentColumnIndex) - 1).$row;
            $colTotalPapa = chr(ord($currentColumnIndex) - 2).$row;
            $colGranTotal = $currentColumnIndex.$row;
            $seccion1 = '='.$colTotalPapa.'+'.$colTotalFanny;

            // Seccion 2 donde en columna quinta se pone la suma de anteriores 4
            // primero me posiciono en la letra final de la seccion y luego resto para seguir arrastrando la letra para futuras secciones
            $currentColumnIndex = siguienteLetra(siguienteLetra(siguienteLetra(siguienteLetra(siguienteLetra($currentColumnIndex)))));
            // despues obtengo las 4 columnas anteriores para la formula
            $colTransfer = chr(ord($currentColumnIndex) - 1).$row;
            $colAbonoViernes = chr(ord($currentColumnIndex) - 2).$row;
            $colAbonoLunes = chr(ord($currentColumnIndex) - 3).$row;
            $colAbonoSabDom = chr(ord($currentColumnIndex) - 4).$row;
            // Hago el arreglo de la formula en variable para facil uso
            $seccion2 = '='.$colAbonoSabDom.'+'.$colAbonoLunes.'+'.$colAbonoViernes.'+'.$colTransfer;

            // Seccion 3 donde solo una columna Abono a fanny
            $currentColumnIndex = siguienteLetra($currentColumnIndex);
            $colTotalAbonos = chr(ord($currentColumnIndex) - 1).$row;
            $seccion3 = '=IF('.$colTotalAbonos.'<='.$colTotalFanny.','.$colTotalAbonos.',IF('.$colTotalAbonos.'>='.$colTotalFanny.','.$colTotalFanny.'))';

            // Seccion 4 donde solo una columna Abono a papa
            $currentColumnIndex = siguienteLetra($currentColumnIndex);
            $colAbonosAFanny = chr(ord($currentColumnIndex) - 1).$row;
            $seccion4 = '='.$colTotalAbonos.'-'.$colAbonosAFanny;

            // Seccion 5 donde solo una columna Resto
            $currentColumnIndex = siguienteLetra($currentColumnIndex);
            $colAbonosAPapa = chr(ord($currentColumnIndex) - 1).$row;
            $seccion5 = '='.$colGranTotal.'-'.$colTotalAbonos;

            // Seccion 6 donde solo una columna Fanny en Grupo restos
            $currentColumnIndex = siguienteLetra($currentColumnIndex);
            $colResto = chr(ord($currentColumnIndex) - 1).$row;
            $seccion6 = '=+'.$colTotalFanny.'-'.$colAbonosAFanny;

            // Seccion 7 donde solo una columna Papa en Grupo restos
            $currentColumnIndex = siguienteLetra($currentColumnIndex);
            $colRestoFanny = chr(ord($currentColumnIndex) - 1).$row;
            $seccion7 = '=+'.$colTotalPapa.'-'.$colAbonosAPapa;

            // Seccion 8 donde solo una columna Comprobacion en Grupo restos
            $currentColumnIndex = siguienteLetra($currentColumnIndex);
            $colRestoPapa = chr(ord($currentColumnIndex) - 1).$row;
            $seccion8 = '=+'.$colGranTotal.'-'.$colTotalAbonos.'-'.$colRestoFanny.'-'.$colRestoPapa;


            // array push final de toda la seccion despues grupos
            array_push($filaDatos,'=ROUND('.$sumaPapa.',0)','=ROUND('.$sumaFanny.',0)',$seccion1,'','','','',$seccion2,$seccion3,$seccion4,$seccion5,$seccion6,$seccion7,$seccion8);

            $sheet->fromArray($filaDatos,NULL,'A'.$row);
            $row++;
        }

        $styleArrayDatosABC = $this->generarEstiloCelda(['horizontal' => Alignment::HORIZONTAL_LEFT,'argb' => hacerColorMasClaro($colorABC),'bold' => false]);
        $sheet->getStyle('A3:A'.$tablaTotal)->applyFromArray($styleArrayDatosABC);
        $sheet->getStyle('B3:B'.$tablaTotal)->applyFromArray($styleArrayDatosABC);
        $sheet->getStyle('C3:C'.$tablaTotal)->applyFromArray($styleArrayDatosABC);

        $sheet->fromArray($filaTotales,NULL,'A'.($tablaTotal+1));
        $styleArrayTotales = $this->generarEstiloCelda(['horizontal' => Alignment::HORIZONTAL_RIGHT,'argb' => 'FF7272','bold' => false,'color' => 'FFFFFF'],true);
        $sheet->mergeCells('A'.($tablaTotal+1).':B'.($tablaTotal+1))->getStyle('A'.($tablaTotal+1).':B'.($tablaTotal+1))->applyFromArray($this->generarEstiloCelda(['argb' => 'FF7272','bold' => false,'color' => 'FFFFFF']));
        $sheet->getStyle('C'.($tablaTotal+1).':'.$letraFin.($tablaTotal+1))->applyFromArray($styleArrayTotales);
        ////////////////////////////////////////////////////////////////////////////
        foreach(range('A', $sheet->getHighestDataColumn()) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        $spreadsheet->getProperties()
        ->setCreator("ElMolino")
        ->setLastModifiedBy("ElMolino")
        ->setTitle("Semana")
        ->setSubject("Semana")
        ->setDescription("Semana")
        ->setKeywords("office 2007 openxml php")
        ->setCategory("ElMolino");
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="Semana.xlsx"');
        $writer->save("php://output");
    }
}
