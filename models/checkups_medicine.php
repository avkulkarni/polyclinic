<?php
class CheckupsMedicine extends AppModel {
    var $belongsTo = array(
        'Medicine', 'Checkup'
    );
}
?>
