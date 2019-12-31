<?php 
  $cars = array();
  $result = $this->query("SELECT * FROM #__avtopark where display=1 ORDER BY position, id ASC");
  if($result) {
    while(false !== ($row = $this->fetch_assoc($result))) {
      $cars[] = $row;
    } 
  }

  foreach($cars as $index => $car) {
    $car['images'] = array(array('title' => $car['name'], 'url' => '/images/cars/main/' . $car['mainimg']));
    $result = $this->query("SELECT * FROM #__avtopark_fotos WHERE pid={$car['id']}");
    while(false !== ($image = $this->fetch_assoc($result))) {
      $url = '/images/cars/' . $image['img'];
      if(file_exists(DOCUMENT_ROOT . $url)) {
        $car['images'][] = array('title' => $image['title'], 'url' => $url);
      }
    }
    $cars[$index] = $car;
  }
  $locations = array();
  $result = $this->query("SELECT * FROM #__locations ORDER BY position, name ASC");
  if($result) {
    while(false !== ($row = $this->fetch_assoc($result))) {
        $locations[$row['id']] = $row['name'];
    }
  }

  include($this->core_get_modtplname("calc.php"));
?>