<?php
if(array_key_exists('Enquete', $campaign)) {
  foreach($campaign['Enquete'] as $enquete) {
    switch($enquete['type']) {
      case 'text':
      case 'textarea':
        echo $this->Form->input($enquete['id'], [
          'type' => $enquete['type'],
          'label' => $enquete['text'],
          'placeholder' => $enquete['help'],
        ]);
        break;
      case 'select':
      case 'radio':
      case 'check':
        $options = [];
        if($enquete['order'] == 'random') {
          shuffle($enquete['EnqueteOption']);
        }
        foreach($enquete['EnqueteOption'] as $enqueteOption) {
          $options[$enqueteOption['id']] = $enqueteOption['text'];
        }
        switch($enquete['type']) {
          case 'select':
            echo $this->Form->input($enquete['id'], [
              'type' => 'select',
              'label' => $enquete['text'],
              'options' => $options,
              'empty' => '選択してください',
              'helpBlock' => $enquete['help'],
            ]);
            break;
          case 'radio':
            echo $this->Form->input($enquete['id'], [
              'type' => 'radio',
              'label' => $enquete['text'],
              'options' => $options,
              'helpBlock' => $enquete['help'],
            ]);
            break;
          case 'check':
            echo $this->Form->input($enquete['id'], [
              'type' => 'select',
              'label' => $enquete['text'],
              'options' => $options,
              'multiple' => 'checkbox',
              'class' => 'checkbox-inline',
              'helpBlock' => $enquete['help'],
            ]);
            break;
        }
        break;
    }
  }
}
