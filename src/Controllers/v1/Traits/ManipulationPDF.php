<?php
 
namespace App\Controllers\v1\Traits;

use App\Utils\LoggerHelper;
use Dompdf\Dompdf;

trait ManipulationPDF {
    public function generatePDF($student, $viewPath, $filePath) :string | null   {
        try {
            // $htmlFilePath = __DIR__ . "/../Resources/Views/contracts/contrato.php";
            $srcDir = dirname(__DIR__, 3);
            $htmlFilePath = "$srcDir$viewPath";
            if (!file_exists($htmlFilePath)) {
                throw new \Exception("Erro: O arquivo do contrato não foi encontrado em: $htmlFilePath");
            }

            ob_start();
            extract(json_decode($student->contrato_infos, true));
            include $htmlFilePath;
            $html = ob_get_clean();

            $dompdf = new Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $pdfOutput = $dompdf->output();
            $pdfPath = "$srcDir$filePath.pdf";

            if (file_put_contents($pdfPath, $pdfOutput) === false) {
                throw new \Exception("Erro ao salvar o arquivo PDF em: $pdfPath");
            }

            return $pdfPath;
        } catch (\Exception $e) {
            dd($e->getMessage());
            LoggerHelper::logError("Erro na geração do PDF: " . $e->getMessage());
            return null;
        }
    }
}