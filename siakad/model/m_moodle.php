<?php
// model mahasiswa
defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

class mMoodle {
    const schema = 'moodle';

    function ReportActivityModul($conn_moodle, $idnumber)
    {
        $periode = Akademik::getPeriode();
        $sql = "select * from moodle.report_activity_url where idnumber='$idnumber' order by section asc";
        return $conn_moodle->getArray($sql);
    }
    function ReportActivityQuiz($conn_moodle, $idnumber)
    {
        $periode = Akademik::getPeriode();
        $sql = "select * from moodle.report_quiz_dosen where kodekelas='$idnumber' order by pertemuanke asc";
        return $conn_moodle->getArray($sql);
    }



}

?>