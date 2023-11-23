<?php

namespace App\Controller\ExtractData;

use App\Entity\Vacation\Vacation;
use App\Repository\CompanyRepository;
use App\Repository\VacationRepository;
use App\Security\ApiTokenHandler;
use DateTime;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ExtractController extends AbstractController
{

    public function __construct(private VacationRepository $vacationRepository,
        private CompanyRepository$companyRepository,
        private ApiTokenHandler $apiTokenHandler
    )
    {
    }

    #[Route('/vacations/extract/{company}')]
    public function generateExcel(Request $request,int $company)
    {
        if($request->query->has("auth"))
        {
            $user = $this->apiTokenHandler->getUserBadgeFrom($request->query->get("auth"));

        }else{
            throw new AuthenticationException("Authorisation Error");
        }

        if( $request->query->has('dateFrom')){
            $dateFrom = DateTime::createFromFormat("Y-m-d", $request->query->get('dateFrom'));
        }else{
            throw new BadRequestException("DateFrom is required");
        }


        if( $request->query->has('dateFrom')){
            $dateFrom = DateTime::createFromFormat("Y-m-d", $request->query->get('dateFrom'));
        }else{
            throw new BadRequestException("DateFrom is required");
        }

        if( $request->query->has('dateTo')){
            $dateTo = DateTime::createFromFormat("Y-m-d", $request->query->get('dateTo'));
        }else{
            throw new BadRequestException("DateTo is required");
        }

        $company = $this->companyRepository->find($company) ?? throw new \Exception("Company is required");

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

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


        $result = $this->vacationRepository->findVacationsToExtract($company, $dateFrom, $dateTo);

        $row = 2;
        foreach ($result as $vacation) {
            if($vacation instanceof Vacation){
                $sheet->setCellValue('A' . $row, $this->formatString(mb_strtoupper($vacation->getEmployee()->getSurname()."_".$vacation->getEmployee()->getName())));
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
        $response->headers->set('Content-Disposition', 'attachment; filename="Extract_Vacations.xlsx"');
        $response->setContent($excelData);

        return $response;
    }

    public function formatString(string $alias)
    {
        $alias= strtolower($alias);
        $alias = str_replace(' ', '-', $alias);

        $alias = strtr($alias, 'ęóąśłżźćń', 'eoaslzzcn');
        $alias = strtr($alias, 'ˇ¦¬±¶Ľ','ASZasz');

        $alias = preg_replace('/[^0-9a-z\-]+/', '', $alias);
        $alias= trim($alias);

        return strtr($alias, ' ','-');
    }
}