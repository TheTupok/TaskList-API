<?php

    class DateService
    {
        public function convertDate($date): string
        {
            $arrDate = explode('-', $date);
            return $arrDate[2] . '.' . $arrDate[1] . '.' . $arrDate[0];
        }
    }