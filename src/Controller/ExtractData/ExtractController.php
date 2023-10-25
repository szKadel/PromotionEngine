<?php

namespace App\Controller\ExtractData;

use App\Entity\Vacation\Vacation;
use App\Repository\VacationRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ExtractController extends AbstractController
{

    public function __construct(private VacationRepository $vacationRepository)
    {
    }

    #[Route('/vacations/extract/')]
    public function generateExcel()
    {
        // Stwórz nowy arkusz Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Dodaj nagłówki kolumn
        $sheet->setCellValue('A1', 'Kod');
        $sheet->setCellValue('B1', 'Nazwisko');
        $sheet->setCellValue('C1', 'Imie');
        $sheet->setCellValue('D1', 'Nazwa_do_importu');
        $sheet->setCellValue('E1', 'Nazwa_zrodlowa');
        $sheet->setCellValue('F1', 'Data_od');
        $sheet->setCellValue('G1', 'Data_do');
        $sheet->setCellValue('H1', 'Przyczyna');
        $sheet->setCellValue('I1', 'Nieobecnosc_na_czesc_dnia');
        $sheet->setCellValue('J1', 'Urlop_na_zadanie');

        // Wypełnij arkusz danymi z bazy danych

        $result = $this->vacationRepository->findAll();

        $row = 2;
        foreach ($result as $vacation) {
            if($vacation instanceof Vacation){
                $sheet->setCellValue('A' . $row, mb_strtoupper($vacation->getEmployee()->getSurname()."_".$vacation->getEmployee()->getName()));
                $sheet->setCellValue('B' . $row, $vacation->getEmployee()->getSurname());
                $sheet->setCellValue('C' . $row, $vacation->getEmployee()->getName());
                $sheet->setCellValue('D' . $row, $vacation->getType()->getName());
                $sheet->setCellValue('E' . $row, $vacation->getType()->getName());
                $sheet->setCellValue('F' . $row, $vacation->getDateFrom()->format('Y-m-d'));
                $sheet->setCellValue('G' . $row, $vacation->getDateTo()->format('Y-m-d'));
                $sheet->setCellValue('H' . $row, "Nie dotyczyy");
                $sheet->setCellValue('I' . $row, "");
                $sheet->setCellValue('J' . $row, 0);
            }
            $row++;
        }

        $response = new Response();
        $writer = new Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');
        $excelData = ob_get_clean();
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="dane.xlsx"');
        $response->setContent($excelData);

        return $response;
    }
}