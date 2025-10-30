<?php

/**
 * Задолжени сте да креирате основен систем за управување со директориум на студенти.
 * Треба да креирате функции за манипулација и прикажување на информации за студентите.
 */
/**
 * Секој студент е претставен како асоцијативна низа со следниве клучеви:
  - name (стринг):
    Целото име на студентот.
  - age (цел број):
    Возраст на студентот.
  - grades (низа од целобројни вредности):
    Низа од цели броеви кои ги претставуваат оценките на студентот (пр. [85, 90, 78]).
* Пример за студент:
  $student = [
  "name" => "John Doe",
  "age" => 20,
  "grades" => [85, 90, 78]
 ];
* Креирај листа на студенти: Креирај низа од 5 студенти, секој со свое име, години, и низа од оценки.
*/

$students = [
    [
        "name" => 'ivan pupinoski',
        "age" => 22,
        "grades" => [85, 90, 78]
    ],
    [
        "name" => 'marija dimitrieska',
        "age" => 23,
        "grades" => [92, 88, 95]
    ],
    [
        "name" => 'john doe',
        "age" => 19,
        "grades" => [70, 75, 80]
    ],
    [
        "name" => 'jane doe',
        "age" => 20,
        "grades" => [88, 84, 90]
    ],
    [
        "name" => 'cristiano ronaldo',
        "age" => 25,
        "grades" => [95, 91, 89]
    ]
]; // Асоцијативна низа од студенти.

/**
 * Дел 1: Пресметување на просечна оценка.
   Функција calculateAverage:
   Напиши функција која како влез зема низа од оценки на студентот
   и враќа просечна оценка.
*/

function calculateAverage($grades){
    $num_of_grades = count($grades);

    if ($num_of_grades === 0)
        return 0;

    $sum_of_grades = array_sum($grades);
    $avg_of_grades = $sum_of_grades/$num_of_grades;

    return $avg_of_grades;
}

/** Дел 2: Филтрирање на студенти по возраст.
 * Функција filterByAge:
   Напиши функција која зема низа од студенти и целобројна вредност за возраст,
   и враќа низа од студенти кои се постари од дадената возраст.
*/

function filterByAge($students, $age_limit){
    $filtered_students = [];

    foreach ($students as $student) {
        if ($student['age'] > $age_limit) {
            $filtered_students[] = $student;
        }
    }

    return $filtered_students;
}

/** Дел 3: Голема буква за имињата на студентите.
 * Функција capitalizeNames:
   Напиши функција која зема низа од студенти
   и ги модифицира имињата така што секој збор од името започнува со голема буква.
*/

function capitalizeNames($students){
    $capitalized_names = [];

    foreach ($students as $student) {
        $student['name'] = ucwords($student['name']);
        $capitalized_names[] = $student;
    }

    return $capitalized_names;
}

/** Дел 4: Прикажување на студенти.
 * Функција displayStudents:
   Напиши функција која ги прикажува информациите за студентите во читлив формат,
   вклучувајќи ја и просечната оценка на секој студент (користи ја функцијата calculateAverage).
   Формат: Name: John Doe, Age: 20, Average Grade: 84.33
*/

function displayStudents($students){
    foreach ($students as $student) {
        echo 'Name: ' . $student['name'] . ', ' .
             'Age: ' . $student['age'] . ', ' .
             'Average Grade: ' . number_format(calculateAverage($student['grades']), 2) . '.';
    }
}

/** Дополнителен дел: Сортирај студенти по име.
 * Функција sortByName:
   Напиши функција која ја сортира низата од студенти азбучно по нивното name.
*/

function sortByName($students){
    usort ($students, function($a, $b){
        return strcmp($a['name'], $b['name']);
    });

    return $students;
}

/** TESTING
echo "Original Students: ";
displayStudents($students);

echo "Function: calculateAverage: ";
foreach ($students as $student) {
    echo $student['name'] . ' ' . number_format(calculateAverage($student['grades']), 2) . '. ';
}

echo "Function: filterByAge: ";
$older_students = filterByAge($students, 23);
displayStudents($older_students);

echo "Function: capitalizeNames: ";
$capitalized_names = capitalizeNames($students);
displayStudents($capitalized_names);

echo "Function: sortByNames: ";
$sorted_name = sortByName($students);
displayStudents($sorted_name);
*/

function displayStudentsCards($students)
{
    echo "<div style='display:flex; flex-wrap:wrap; gap:15px;'>"; // container flex
    foreach ($students as $student) {
        $avg = number_format(calculateAverage($student['grades']), 2);
        if ($avg >= 90) {
            $color = "#28a745"; // green
        } elseif ($avg >= 75) {
            $color = "#fd7e14"; // orange
        } else {
            $color = "#dc3545"; // red
        }

        echo "<div style='flex:1 1 200px; border:1px solid #ccc; border-radius:10px; padding:15px; box-shadow:2px 2px 8px rgba(0,0,0,0.1);'>";
        echo "<h3 style='margin:0 0 5px 0; color:#007bff;'>" . $student['name'] . "</h3>";
        echo "<p style='margin:5px 0;'>Age: <strong>" . $student['age'] . "</strong></p>";
        echo "<p style='margin:5px 0;'>Average Grade: <strong style='color:$color;'>" . $avg . "</strong></p>";
        echo "</div>";
    }
    echo "</div>";
}

echo "<h2 style='color: darkblue;'>Original Students:</h2>";
displayStudentsCards($students);

echo "<h2 style='color: darkgreen;'>Function: calculateAverage:</h2>";
displayStudentsCards($students);

echo "<h2 style='color: darkred;'>Function: filterByAge (older than 23):</h2>";
$older_students = filterByAge($students, 23);
displayStudentsCards($older_students);

echo "<h2 style='color: purple;'>Function: capitalizeNames:</h2>";
$capitalized_names = capitalizeNames($students);
displayStudentsCards($capitalized_names);

echo "<h2 style='color: orange;'>Function: sortByName:</h2>";
$sorted_name = sortByName($students);
displayStudentsCards($sorted_name);

?>