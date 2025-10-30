<?php

/**
 * Задолжени сте да изградите едноставен систем за управување со пациенти во мала клиника.
*/
/**
 * За пациентите се чуваат:
   - id - int
   - name - string
   - medicalHistory - енумерирана низа на претходни дијагнози од доктори
   - treatmentHistory - енумерирана низа на претходни третмани од доктори
*/

class Patient {
    public int $id;
    public string $name;
    public array $medicalHistory = [];
    public array $treatmentHistory = [];

    public function __construct(int $id, string $name) {
        $this->id = $id;
        $this->name = $name;
    }

    public function addDiagnose($diagnosis): void {
        $this->medicalHistory[] = $diagnosis;
    }

    public function addTreatment($treatment): void {
        $this->treatmentHistory[] = $treatment;
    }

    public function getMedicalHistory(): array {
        return $this->medicalHistory;
    }

    public function getTreatmentHistory(): array {
        return $this->treatmentHistory;
    }
}

/**
 * Да се дефинира апстрактна класа Doctor која содржи:
   - id - string
   - специјализација(enum, FAMILY_MEDICINE, CARDIOLOGY, NEUROLOGY, RADIOLOGY)
   - години искуство
   - список на пациенти - асоцијативна низа на пациенти каде клуч е id-то на пациентот.
*/

enum Specialization: string {
    case FAMILY_MEDICINE = 'family_medicine';
    case CARDIOLOGY = 'cardiology';
    case NEUROLOGY = 'neurology';
    case RADIOLOGY = 'radiology';
}

abstract class Doctor {
    public string $id;
    public string $name;
    public Specialization $specialization;
    public int $years_of_experience;
    /** @var array<int, Patient> */
    public array $patients = [];

    public function __construct(string $id, string $name, int $years_of_experience, Specialization $specialization) {
        $this->id = $id;
        $this->name = $name;
        $this->specialization = $specialization;
        $this->years_of_experience = $years_of_experience;
    }

    public function addPatient(Patient $patient): void {

        foreach ($this->patients as $existing_patient) {
            if ($existing_patient->id === $patient->id) {
                return;
            }
        }

        $this->patients[$patient->id] = $patient;
    }

    public function printPatients(): void {
        if (empty($this->patients)) {
            echo "Doctor {$this->name} has no patients.\n";
            return;
        }

        echo "Patients of {$this->name} ({$this->specialization->value}):\n";
        foreach ($this->patients as $patient) {
            echo "  - ID: {$patient->id}, Name: {$patient->name}\n";
        }
    }
}

/**
 * Да се дефинира trait Treatable кој ќе имплементира метод:
- diagnose(Patient $patient, string $diagnosis)
кој овозможува матичниот доктор да додаде дијагноза на пациентот.
 */

trait Treatable {

    public function diagnose(Patient $patient, string $diagnosis): void {
        # $patient->medicalHistory[] = $diagnosis;
        $patient->addDiagnose($diagnosis);
    }
}

/**
 * Исто така потребно е да се дефинираат две класи FamilyDoctor и Specialist кои ќе наследуваат од класата Doctor.
  - FamilyDoctor има фиксна специјализација: FAMILY_MEDICINE, за разлика од тоа,
  - Specialist може да има било каква специјализација.
 * За FamilyDoctor да се имплементира функција:
   - refer(Patient $patient, array $doctors, Specialization $specialization): Doctor
     која го додава дадениот пациент во листата на специјалистот со најмногу искуство.
 * Напомена: Пациентот не смее да го има повеќе од еднаш во листата на пациентите на докторот.
*/

class FamilyDoctor extends Doctor {
    use Treatable;

    public function __construct(string $id, string $name, int $years_of_experience) {
        parent::__construct($id, $name, $years_of_experience, Specialization::FAMILY_MEDICINE);
    }

    public function refer(Patient $patient, array $doctors, Specialization $specialization): Doctor {
        $available_doctors = [];
        foreach ($doctors as $doctor) {
            if ($doctor instanceof Specialist && $doctor->specialization === $specialization) {
                $available_doctors[] = $doctor;
            }
        }

        $best_specialist = $available_doctors[0];
        foreach ($available_doctors as $available) {
            if ($available->years_of_experience > $best_specialist->years_of_experience) {
                $best_specialist = $available;
            }
        }

        $alr_referred = FALSE;
        foreach ($best_specialist->patients as $existing_patient) {
            if ($existing_patient->id === $patient->id) {
                $alr_referred = TRUE;
                break;
            }
        }

        if ($alr_referred) {
            echo "Patient with id {$patient->id} is already referred to {$best_specialist->name}\n";
        } else {
            $best_specialist->addPatient($patient);
        }

        return $best_specialist;
    }
}

class Specialist extends Doctor {
    use Treatable;

    public function __construct(string $id, string $name, int $years_of_experience, Specialization $specialization) {
        parent::__construct($id, $name, $years_of_experience, $specialization);
    }

    public function treatPatient(Patient $patient, string $treatment): void {
        $patient->addTreatment($treatment);

        foreach ($this->patients as $key => $p) {
            if ($p->id === $patient->id) {
                unset($this->patients[$key]);
                echo "Patient {$patient->name} treated with '{$treatment}' and removed from {$this->name}'s list.\n";
                return;
            }
        }

        echo "Patient with id {$patient->id} not found in {$this->name}'s list.\n";
    }
}

/**
 * Да се креира енумерацијата Specialization. DONE!
 * Да се креира trait Treatable и да се имплементира функцијата diagnose. DONE!
 * Да се имплементираат наведените класи и потребните методи за истите. DONE!
 * Да се креира helper функција addPatient(Patient $patient) која додава пациент во листата на докторот. DONE!
 * Да се креира функција printPatients() која печати листа на паценти во конзола..
 * Да се имплементира refer функцијата. DONE!
 * Да се креира функција treatPatient со која специјалистите
   додаваат запис во историјата третмани на пациентот и
   го бришат од својот список на пациенти. DONE!
*/

// Create patients
$john = new Patient(1, "John Doe");
$jane = new Patient(2, "Jane Smith");

// Create doctors
$familyDoctor = new FamilyDoctor("D001", "Dr. Brown", 12);
$cardiologist1 = new Specialist("D002", "Dr. Heart", 8, Specialization::CARDIOLOGY);
$cardiologist2 = new Specialist("D003", "Dr. Pulse", 15, Specialization::CARDIOLOGY);
$neurologist = new Specialist("D004", "Dr. Brain", 10, Specialization::NEUROLOGY);

// Add patient to family doctor
$familyDoctor->addPatient($john);
$familyDoctor->diagnose($john, 'High blood pressure');
// Print before referral
$familyDoctor->printPatients();

// Refer John to cardiologist (most experienced one)
$treatingDoctor = $familyDoctor->refer($john, [$cardiologist1, $cardiologist2, $neurologist], Specialization::CARDIOLOGY);
echo "Referred patient with id $john->id to doctor $treatingDoctor->name\n";

// Refer the same patient again (should return that patient is already referred)
$treatingDoctor = $familyDoctor->refer($john, [$cardiologist1, $cardiologist2, $neurologist], Specialization::CARDIOLOGY);

$treatingDoctor->printPatients();

if ($treatingDoctor instanceof Specialist) {
    $treatingDoctor->treatPatient($john, 'Beta-blockers');
}

// Print specialists’ patients after referral
$treatingDoctor->printPatients();

// Show John’s medical history
echo "\nMedical history of {$john->name}:\n";
foreach ($john->getMedicalHistory() as $record) {
    echo "- $record\n";
}

?>