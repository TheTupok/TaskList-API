<?php

    class DateService
    {
        public function convertDate($date): string
        {
            if ($date) {
                $arrDate = explode('-', $date);
                return $arrDate[2] . '.' . $arrDate[1] . '.' . $arrDate[0];
            } else {
                return '';
            }
        }

        public function getCurrentDate(): string {
            return date('d.m.Y');
        }
    }