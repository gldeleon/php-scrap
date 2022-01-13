<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ResponseController as RC;
use Illuminate\Support\Facades\DB;

class PatientController extends RC {

    public function search($name) {
        $patname = urldecode($name);
        $query = "SELECT
                    DISTINCT
                            p.person_id AS idPerson,
                            p2.pat_id AS idPatient,
                            p.per_complete AS namePerson
                    FROM
                            person p
                    LEFT JOIN patient p2 ON
                            p.person_id = p2.person_id
                    LEFT JOIN patagreement p3 ON
                            p2.pat_id = p3.pat_id
                    WHERE
                            p.per_complete like '%$patname%'
                            AND p2.pat_active = 'activo'";

        $patients = DB::select($query);
        return $this->sendResponse($patients, 'ok');
    }

    public function searchById($pat_id) {
//        armamos y devolvemos en un array //
        $patientData = [
            "patData" => $this->patDetails($pat_id),
//            "patBudget" => $this->patBudgets($pat_id),
            "patDocuments" => $this->patDocuments($pat_id),
            "patSessions" => $this->patSessions($pat_id)
        ];

        return $this->sendResponse($patientData, 'ok');
    }

    private function patDetails($pat_id) {
        $query = "SELECT
                    DISTINCT
                            p2.pat_id AS idPatient,
                            p.per_lastname AS aPaterno,
                            p.per_surename AS aMaterno,
                            p.per_name AS nombre,
                            p.per_complete AS nombreCompleto,
                            p.per_birthday AS fnacimiento,
                            p3.agr_id AS idPlan,
                            a.agr_name AS nombrePlan,
                            e.email AS email,
                            t.tel_number AS telefono
                    FROM
                            person p
                    LEFT JOIN patient p2 ON
                            p.person_id = p2.person_id
                    LEFT JOIN patagreement p3 ON
                            p2.pat_id = p3.pat_id
                    LEFT JOIN agreement a ON
                            p3.agr_id = a.agr_id
                    LEFT JOIN email e ON
                            p.person_id = e.person_id
                    LEFT JOIN telephone t ON
                            p.person_id = t.person_id
                    WHERE
                            p2.pat_id = $pat_id
                            AND a.agr_active = 'activo'
                            -- AND e.email_active = 'activo'
                            -- AND t.tel_active = 'activo'
                            AND p2.pat_active = 'activo'
                    GROUP BY p2.pat_id,p.per_lastname,p.per_surename,p.per_name,p.per_birthday,p3.agr_id,a.agr_name,e.email,t.tel_number
                    LIMIT 1";
        $patient = DB::select($query);
        return $patient;
    }

    private function patBudgets() {

    }

    private function patDocuments($pat_id) {
        $query = "SELECT
                           DISTINCT
                            f.*,
                            c.cli_name,
                            case
                                    when (pm.paymeth_abbr <> 'MS'
                                            and pm.paymeth_abbr <> 'RM'
                                            and pm.paymeth_abbr <> 'TR')
                                                            then '--'
                                    else pm.paymeth_abbr
                            end as paymeth
                    FROM
                            file f
                    LEFT JOIN filepaymethod fp ON
                            f.file_id = fp.file_id
                    LEFT JOIN paymethod pm ON
                            fp.paymeth_id = pm.paymeth_id
                    LEFT JOIN clinic c ON
                            f.cli_id = c.cli_id
                    WHERE
                            f.filetype_id = '1'
                            AND f.pat_id = $pat_id
                            AND fp.paymeth_id NOT IN (7)
                    ORDER BY
                            f.file_date,
                            f.file_number,
                            f.cli_id";
        $patDocuments = DB::select($query);
        return $patDocuments;
    }

    private function patSessions($pat_id) {

        $query = "select
                        f.file_date,
                        c.cli_name,
                        f2.emp_id,
                        e.emp_abbr,
                        t.trt_name,
                        f.file_comment,
                        t2.tht_num,
                        f2.sessnum,
                        f2.lastsess,
                        f2.quantity,
                        f3.file_rel_id,
                        (
                        select
                                file_number
                        from
                                file
                        where
                                file_id = f3.file_id) as recibo
                FROM
                        file f
                LEFT JOIN fileentry f2 ON
                        f.file_id = f2.file_id
                LEFT JOIN filereference f3 ON
                        f.file_id = f3.file_rel_id and f3.active = 'activo'
                LEFT JOIN clinic c ON
                        f.cli_id = c.cli_id
                LEFT JOIN employee e ON
                        f2.emp_id = e.emp_id
                LEFT JOIN treatment t ON
                        f2.trt_id = t.trt_id
                LEFT JOIN tooth t2 ON
                        f2.tht_id = t2.tht_id
                WHERE
                        f.status_id != 2
                        and pat_id = $pat_id
                        and filetype_id = 2";
        $patSessions = DB::select($query);
        return $patSessions;
    }

}
