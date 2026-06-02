<?php 
function dynamic_options(array $options) { 
    $finalOptions = []; 
    foreach ($options as $label=>$details) { 
        if (!is_string($label) || !is_array($details)) continue; 
        if (trim($label) === '') continue; 
        if (count($details) !== 2) continue; 
        if (!is_string($details[0])) continue; 
        if (!is_bool($details[1])) continue; 

        $sanitizedLabel = htmlspecialchars($label); 
        $sanitizedValue = htmlspecialchars($details[0]); 
        $willShow = $details[1]; 

        if ($willShow === true) { 
            $opHTML = <<< HTML
                <option value="$sanitizedValue">$sanitizedLabel</option> 
            HTML; 
            array_push($finalOptions, $opHTML); 
        } 
    } 
    $output = implode("\n", $finalOptions); 
    return $output; 
} 
?>