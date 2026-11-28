<?php
class ResidentProfile
{
    private const BENEFITS = ['LGU', 'DSWD', 'CSWDO', 'CHED', 'NGO', 'Athlete', 'Scholar'];
    private const SENIOR_PROGRAMS = ['LGU', 'DSWD / SOCPEN', 'CSWDO', 'SSS', 'GSIS', 'PAGIBIG', 'Private Agency', 'Insurance'];

    public static function from_post(array $post)
    {
        $fields = [
            'firstname' => 100, 'middlename' => 100, 'lastname' => 100,
            'suffix' => 20, 'nickname' => 100, 'birthdate' => 10,
            'move_in_date' => 10, 'residency_type' => 30,
            'gender' => 10, 'civilstatus' => 50, 'contact' => 11,
            'housenumber' => 50, 'street' => 100, 'barangay' => 100,
            'city' => 100, 'province' => 100, 'zipcode' => 4,
            'birthplace' => 100, 'occupation' => 100, 'monthly_income' => 20,
            'education' => 100, 'course' => 100, 'talents' => 1000,
            'marketable_skills' => 1000, 'health_condition' => 2000,
            'transport_mode' => 50, 'transport_details' => 255,
            'soloparent' => 20, 'children' => 2, 'member' => 20,
            'program' => 255, 'dependent' => 20, 'relationship' => 100,
            'school' => 150, 'yearlevel' => 50, 'enrolled' => 50,
            'attained' => 100, 'issues' => 5000,
        ];
        $data = [];
        foreach ($fields as $field => $limit) {
            $value = $post[$field] ?? '';
            if (!is_string($value)) {
                return ['data' => [], 'error' => 'Enter valid information in all profile fields.'];
            }
            $data[$field] = trim($value);
            if (strlen($data[$field]) > $limit) {
                return ['data' => $data, 'error' => 'One or more profile fields exceed the allowed length.'];
            }
        }

        foreach ([
            'benefits' => self::BENEFITS,
            'senior' => self::SENIOR_PROGRAMS,
        ] as $field => $allowedValues) {
            $values = $post[$field] ?? [];
            if (!is_array($values)) {
                return ['data' => $data, 'error' => 'Select valid assistance program options.'];
            }
            foreach ($values as $value) {
                if (!is_string($value) || !in_array($value, $allowedValues, true)) {
                    return ['data' => $data, 'error' => 'Select valid assistance program options.'];
                }
            }
            $data[$field] = json_encode(array_values(array_unique($values)));
        }

        foreach (['firstname', 'lastname', 'contact', 'housenumber', 'barangay', 'city', 'province', 'zipcode', 'birthplace'] as $required) {
            if ($data[$required] === '') {
                return ['data' => $data, 'error' => 'Complete all required resident profile fields.'];
            }
        }
        if (!in_array($data['gender'], ['Male', 'Female', 'Other'], true)) {
            return ['data' => $data, 'error' => 'Select a valid gender.'];
        }
        if (!in_array($data['civilstatus'], ['Single', 'Married', 'Widowed', 'Annulled'], true)) {
            return ['data' => $data, 'error' => 'Select a valid civil status.'];
        }
        if (!preg_match('/^[0-9]{11}$/', $data['contact'])) {
            return ['data' => $data, 'error' => 'Enter a contact number using exactly 11 digits.'];
        }

        $birthdate = DateTime::createFromFormat('!Y-m-d', $data['birthdate']);
        if (
            !$birthdate ||
            $birthdate->format('Y-m-d') !== $data['birthdate'] ||
            $birthdate > new DateTime('today')
        ) {
            return ['data' => $data, 'error' => 'Enter a valid date of birth.'];
        }
        $data['age'] = (int)$birthdate->diff(new DateTime('today'))->y;
        if ($data['age'] > 127) {
            return ['data' => $data, 'error' => 'Enter a valid date of birth.'];
        }

        if (!in_array($data['residency_type'], ['Permanent Resident', 'Temporary Resident'], true)) {
            return ['data' => $data, 'error' => 'Select a valid residency type.'];
        }
        if ($data['residency_type'] === 'Temporary Resident') {
            $moveInDate = DateTime::createFromFormat('!Y-m-d', $data['move_in_date']);
            if (
                !$moveInDate ||
                $moveInDate->format('Y-m-d') !== $data['move_in_date'] ||
                $moveInDate > new DateTime('today')
            ) {
                return ['data' => $data, 'error' => 'Enter a valid date moved into the barangay.'];
            }
        } else {
            $data['move_in_date'] = '';
        }
        if (!preg_match('/^[0-9]{4}$/', $data['zipcode'])) {
            return ['data' => $data, 'error' => 'Enter a valid 4-digit ZIP code.'];
        }

        if ($data['monthly_income'] !== '') {
            if (!is_numeric($data['monthly_income']) || (float)$data['monthly_income'] < 0 || (float)$data['monthly_income'] > 9999999999.99) {
                return ['data' => $data, 'error' => 'Enter a valid monthly income.'];
            }
            $data['monthly_income'] = number_format((float)$data['monthly_income'], 2, '.', '');
        }
        if ($data['children'] !== '' && (!ctype_digit($data['children']) || (int)$data['children'] > 99)) {
            return ['data' => $data, 'error' => 'Enter a valid number of children.'];
        }
        foreach (['soloparent', 'member', 'dependent'] as $field) {
            if (!in_array($data[$field], ['', 'Yes', 'No'], true)) {
                return ['data' => $data, 'error' => 'Select a valid Yes or No answer.'];
            }
        }
        if (!in_array($data['education'], ['', 'Elementary', 'High School', 'Senior High School', 'College', 'Graduate', 'Vocational'], true)) {
            return ['data' => $data, 'error' => 'Select a valid educational attainment.'];
        }
        if (!in_array($data['transport_mode'], ['', 'Public transportation', 'Personal vehicle', 'Company service', 'Walking', 'Other'], true)) {
            return ['data' => $data, 'error' => 'Select a valid transportation mode.'];
        }

        return ['data' => $data, 'error' => null];
    }

    public static function aid_eligibility_date($moveInDate)
    {
        if (!is_string($moveInDate)) {
            return null;
        }

        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $moveInDate);
        if (
            !$date ||
            $date->format('Y-m-d') !== $moveInDate ||
            $date > new DateTimeImmutable('today')
        ) {
            return null;
        }

        $eligibilityMonth = $date->modify('first day of this month')->modify('+6 months');
        $day = min((int)$date->format('j'), (int)$eligibilityMonth->format('t'));

        return $eligibilityMonth->setDate(
            (int)$eligibilityMonth->format('Y'),
            (int)$eligibilityMonth->format('n'),
            $day
        );
    }
}
