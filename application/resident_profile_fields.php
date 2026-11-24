<?php
$profileValues = isset($profileValues) && is_array($profileValues) ? $profileValues : [];
$profileValue = function ($field) use ($profileValues) {
    $value = $profileValues[$field] ?? '';
    return htmlspecialchars(is_scalar($value) ? (string)$value : '', ENT_QUOTES, 'UTF-8');
};
$profileSelections = function ($field) use ($profileValues) {
    $value = $profileValues[$field] ?? [];
    if (is_string($value)) {
        $decoded = json_decode($value, true);
        $value = is_array($decoded) ? $decoded : [];
    }
    return is_array($value) ? $value : [];
};
$profileChecked = function ($field, $value) use ($profileSelections) {
    return in_array($value, $profileSelections($field), true) ? 'checked' : '';
};
?>
<section class="application-form-section" aria-labelledby="personal-heading">
    <h4 id="personal-heading">Personal Information</h4>
    <div class="application-fields-grid">
        <label>First name *
            <input class="form-control" type="text" name="firstname" maxlength="100" value="<?= $profileValue('firstname') ?>" required>
        </label>
        <label>Middle name
            <input class="form-control" type="text" name="middlename" maxlength="100" value="<?= $profileValue('middlename') ?>">
        </label>
        <label>Last name *
            <input class="form-control" type="text" name="lastname" maxlength="100" value="<?= $profileValue('lastname') ?>" required>
        </label>
        <label>Suffix
            <input class="form-control" type="text" name="suffix" maxlength="20" placeholder="Jr., Sr., III" value="<?= $profileValue('suffix') ?>">
        </label>
        <label>Nickname
            <input class="form-control" type="text" name="nickname" maxlength="100" value="<?= $profileValue('nickname') ?>">
        </label>
        <label>Date of birth *
            <input class="form-control" type="date" id="resident-birthdate" name="birthdate" max="<?= date('Y-m-d') ?>" value="<?= $profileValue('birthdate') ?>" required>
        </label>
        <label>Age (calculated)
            <input class="form-control" type="number" id="resident-age" value="<?= $profileValue('age') ?>" readonly>
        </label>
        <label>Gender *
            <select class="form-control" name="gender" required>
                <option value="">Select gender</option>
                <?php foreach (['Male', 'Female', 'Other'] as $option): ?>
                    <option value="<?= $option ?>" <?= ($profileValues['gender'] ?? '') === $option ? 'selected' : '' ?>><?= $option ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Civil status *
            <select class="form-control" name="civilstatus" required>
                <option value="">Select civil status</option>
                <?php foreach (['Single', 'Married', 'Widowed', 'Annulled'] as $option): ?>
                    <option value="<?= $option ?>" <?= ($profileValues['civilstatus'] ?? '') === $option ? 'selected' : '' ?>><?= $option ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Contact number *
            <input class="form-control" type="tel" name="contact" maxlength="11" pattern="[0-9]{7,11}" placeholder="Digits only" value="<?= $profileValue('contact') ?>" required>
        </label>
        <label>Place of birth
            <input class="form-control" type="text" name="birthplace" maxlength="100" value="<?= $profileValue('birthplace') ?>" data-application-required <?= !empty($requireAidProfileFields) ? 'required' : '' ?>>
        </label>
    </div>
</section>

<script>
    const residentBirthdate = document.getElementById('resident-birthdate');
    const residentAge = document.getElementById('resident-age');
    if (residentBirthdate && residentAge) {
        const updateResidentAge = () => {
            if (!residentBirthdate.value) {
                residentAge.value = '';
                return;
            }
            const [year, month, day] = residentBirthdate.value.split('-').map(Number);
            const today = new Date();
            let age = today.getFullYear() - year;
            if (
                today.getMonth() + 1 < month ||
                (today.getMonth() + 1 === month && today.getDate() < day)
            ) {
                age -= 1;
            }
            residentAge.value = String(age);
        };
        residentBirthdate.addEventListener('input', updateResidentAge);
        updateResidentAge();
    }
</script>

<section class="application-form-section" aria-labelledby="address-heading">
    <h4 id="address-heading">Residential Address</h4>
    <div class="application-fields-grid">
        <label>House / lot number *
            <input class="form-control" type="text" name="housenumber" maxlength="50" value="<?= $profileValue('housenumber') ?>" required>
        </label>
        <label>Street
            <input class="form-control" type="text" name="street" maxlength="100" value="<?= $profileValue('street') ?>">
        </label>
        <label>Barangay *
            <input class="form-control" type="text" name="barangay" maxlength="100" value="<?= $profileValue('barangay') ?>" required>
        </label>
        <label>Date moved into this barangay *
            <input class="form-control" type="date" name="move_in_date" max="<?= date('Y-m-d') ?>" value="<?= $profileValue('move_in_date') ?>" <?= !empty($requireAidProfileFields) ? 'readonly' : '' ?> required>
            <?php if (!empty($requireAidProfileFields)): ?>
                <span class="application-help">This date is used to assess your six-month residency requirement.</span>
            <?php endif; ?>
        </label>
        <label>City / municipality *
            <input class="form-control" type="text" name="city" maxlength="100" value="<?= $profileValue('city') ?>" required>
        </label>
        <label>Province *
            <input class="form-control" type="text" name="province" maxlength="100" value="<?= $profileValue('province') ?>" required>
        </label>
        <label>ZIP code
            <input class="form-control" type="text" name="zipcode" maxlength="20" value="<?= $profileValue('zipcode') ?>">
        </label>
    </div>
</section>

<section class="application-form-section" aria-labelledby="education-heading">
    <h4 id="education-heading">Education and Livelihood</h4>
    <div class="application-fields-grid">
        <label>Highest educational attainment
            <select class="form-control" name="education" data-application-required <?= !empty($requireAidProfileFields) ? 'required' : '' ?>>
                <option value="">Select attainment</option>
                <?php foreach (['Elementary', 'High School', 'Senior High School', 'College', 'Graduate', 'Vocational'] as $option): ?>
                    <option value="<?= $option ?>" <?= ($profileValues['education'] ?? '') === $option ? 'selected' : '' ?>><?= $option ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Course / major
            <input class="form-control" type="text" name="course" maxlength="100" value="<?= $profileValue('course') ?>">
        </label>
        <label>Occupation
            <input class="form-control" type="text" name="occupation" maxlength="100" value="<?= $profileValue('occupation') ?>" data-application-required <?= !empty($requireAidProfileFields) ? 'required' : '' ?>>
        </label>
        <label>Monthly income (PHP)
            <input class="form-control" type="number" name="monthly_income" min="0" step="0.01" value="<?= $profileValue('monthly_income') ?>">
        </label>
        <label>Talents / skills
            <textarea class="form-control" name="talents" rows="2" maxlength="1000"><?= $profileValue('talents') ?></textarea>
        </label>
        <label>Profitable / marketable skills
            <textarea class="form-control" name="marketable_skills" rows="2" maxlength="1000"><?= $profileValue('marketable_skills') ?></textarea>
        </label>
    </div>
</section>

<section class="application-form-section" aria-labelledby="health-heading">
    <h4 id="health-heading">Health and Transportation</h4>
    <div class="application-fields-grid">
        <label>Health conditions (if any)
            <textarea class="form-control" name="health_condition" rows="3" maxlength="2000" placeholder="List any conditions or enter None"><?= $profileValue('health_condition') ?></textarea>
        </label>
        <label>Mode of transportation
            <select class="form-control" name="transport_mode">
                <option value="">Select mode</option>
                <?php foreach (['Public transportation', 'Personal vehicle', 'Company service', 'Walking', 'Other'] as $option): ?>
                    <option value="<?= $option ?>" <?= ($profileValues['transport_mode'] ?? '') === $option ? 'selected' : '' ?>><?= $option ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label class="application-full">Vehicle / transportation details
            <input class="form-control" type="text" name="transport_details" maxlength="255" value="<?= $profileValue('transport_details') ?>">
        </label>
    </div>
</section>

<section class="application-form-section" aria-labelledby="assistance-heading">
    <h4 id="assistance-heading">Family and Assistance Information</h4>
    <div class="application-fields-grid">
        <label>Solo parent?
            <select class="form-control" name="soloparent">
                <option value="">Select an answer</option>
                <?php foreach (['Yes', 'No'] as $option): ?>
                    <option value="<?= $option ?>" <?= ($profileValues['soloparent'] ?? '') === $option ? 'selected' : '' ?>><?= $option ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Number of children
            <input class="form-control" type="number" name="children" min="0" max="99" value="<?= $profileValue('children') ?>">
        </label>
        <label>4Ps member?
            <select class="form-control" name="member">
                <option value="">Select an answer</option>
                <?php foreach (['Yes', 'No'] as $option): ?>
                    <option value="<?= $option ?>" <?= ($profileValues['member'] ?? '') === $option ? 'selected' : '' ?>><?= $option ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>4Ps program / set
            <input class="form-control" type="text" name="program" maxlength="255" value="<?= $profileValue('program') ?>">
        </label>
        <label>OFW dependent?
            <select class="form-control" name="dependent">
                <option value="">Select an answer</option>
                <?php foreach (['Yes', 'No'] as $option): ?>
                    <option value="<?= $option ?>" <?= ($profileValues['dependent'] ?? '') === $option ? 'selected' : '' ?>><?= $option ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Relationship to OFW
            <input class="form-control" type="text" name="relationship" maxlength="100" value="<?= $profileValue('relationship') ?>">
        </label>
        <div class="application-full">
            <span class="application-label">Educational assistance / benefits</span>
            <div class="application-choice-list">
                <?php foreach (['LGU', 'DSWD', 'CSWDO', 'CHED', 'NGO', 'Athlete', 'Scholar'] as $option): ?>
                    <label><input type="checkbox" name="benefits[]" value="<?= $option ?>" <?= $profileChecked('benefits', $option) ?>><?= $option ?></label>
                <?php endforeach; ?>
            </div>
        </div>
        <label>School
            <input class="form-control" type="text" name="school" maxlength="150" value="<?= $profileValue('school') ?>">
        </label>
        <label>Year level / grade
            <input class="form-control" type="text" name="yearlevel" maxlength="50" value="<?= $profileValue('yearlevel') ?>">
        </label>
        <label>Last year enrolled (out-of-school youth)
            <input class="form-control" type="text" name="enrolled" maxlength="50" value="<?= $profileValue('enrolled') ?>">
        </label>
        <label>Last year attained
            <input class="form-control" type="text" name="attained" maxlength="100" value="<?= $profileValue('attained') ?>">
        </label>
        <div class="application-full">
            <span class="application-label">Senior citizen programs acquired</span>
            <div class="application-choice-list">
                <?php foreach (['LGU', 'DSWD / SOCPEN', 'CSWDO', 'SSS', 'GSIS', 'PAGIBIG', 'Private Agency', 'Insurance'] as $option): ?>
                    <label><input type="checkbox" name="senior[]" value="<?= $option ?>" <?= $profileChecked('senior', $option) ?>><?= $option ?></label>
                <?php endforeach; ?>
            </div>
        </div>
        <label class="application-full">Issues and concerns
            <textarea class="form-control" name="issues" rows="3" maxlength="5000"><?= $profileValue('issues') ?></textarea>
        </label>
    </div>
</section>
