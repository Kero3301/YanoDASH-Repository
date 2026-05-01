<?php
    require_once dirname(__DIR__). '/utils/loader.php';
    load_utils('text_utils');
    global $app_url;

    echo <<< HTML
        <link rel="stylesheet" type="text/css" href="$app_url/css/components/user-form.css">
    HTML;

    /* 
        HOW TO USE:
        
        <?php 
            echo userForm(
                "(your form's ID here)",
                "(your form's text label here)",
                [
                    formGroup("(your form group's label)", "(id of element the label is for)", "input("(same as id of element the label is for)", $type)"),
                    formGroup("(your form group's label)", "(id of element the label is for)", "fileUpload("(same as id of element the label is for)", $acceptedFileTypes, ...)"),
                    ...
                ],
                "(name or link of the PHP script you want this form to run when submitted)",
                submitButtonText: "(the text you want for the Submit button's label)",
                precontent: <<< HTML
                    (type here the HTML for the buttons, e.g. "Back", and other content you want to appear on the top part of the form)
                HTML
            )
        ?>
    */
    function user_form(
        string $id, 
        string $label,
        array $formGroups, 
        string $action,
        string $method = "POST",
        string $enctype = "multipart/form-data",
        string $submitButtonText = "Submit",
        string $precontent = "",
        string $postcontent = ""
    ) {
        # Sanitization
        $sanitizedID = htmlspecialchars(normalize_identifier($id));
        $sanitizedLabel = htmlspecialchars($label);
        $sanitizedAction = htmlspecialchars($action);
        $sanitizedMethod = htmlspecialchars($method);
        $sanitizedEnctype = htmlspecialchars($enctype);
        $sanitizedSubmitButtonText = htmlspecialchars($submitButtonText);

        # ID Derivation
        $formID = $sanitizedID. "-form";
        $submissionName = $sanitizedID. "-submit";

        # Content definition and population
        $formGroupsHTML = count($formGroups) > 0
            ? implode("\n", $formGroups)
            : "";

        # Structure definition and construction
        return <<< HTML
            <div class="form-container">
                $precontent
                <h2>$sanitizedLabel</h2>
                <form action="$sanitizedAction" method="$sanitizedMethod" enctype="$sanitizedEnctype">
                    $formGroupsHTML
                    <button type="submit" name="$submissionName" class="btn-submit">$sanitizedSubmitButtonText</button>
                </form>
            </div>
        HTML;
    }

    function form_group(string $label, string $labelFor, string $content, bool $inline = false) {
        $sanitizedLabel = htmlspecialchars($label);
        $separation = $inline? "" : "<br>";

        return <<< HTML
            <div class="form-group">
                <label for="$labelFor">$sanitizedLabel</label>
                $separation
                $content
            </div>
        HTML;
    }

    function options(string $id, array $options) {
        $sanitizedID = htmlspecialchars(normalize_identifier($id));
        
        $optionList = [];
        foreach ($options as $value) {
            $sanitizedValue = htmlspecialchars($value);

            $optionHTML = <<< HTML
                <option id="$sanitizedID" value="$sanitizedValue">
                    $sanitizedValue
                </option>
            HTML;
            array_push($optionList, $optionHTML);
        }
        $optionListHTML = count($optionList) > 0 
            ? implode("\n", $optionList) 
            : <<< HTML
                <option>(empty)</option>
            HTML;

        return <<< HTML
            <select id="$sanitizedID" name="$sanitizedID">
                $optionListHTML
            </select>
        HTML;
    }

    function input(string $id, string $type = "text", bool $required = true, string $placeholder = null) {
        $sanitizedID = htmlspecialchars(normalize_identifier($id));
        $sanitizedInputType = htmlspecialchars($type);
    
        $requirement = $required? "required" : "";
        $placeholderContent = "";
        if ($placeholder !== null && $placeholder !== "") {
            $sanitizedPlaceholder = htmlspecialchars($placeholder);
            $placeholderContent = "placeholder=\"$sanitizedPlaceholder\"";
        }

        return <<< HTML
            <input id="$sanitizedID" name="$sanitizedID" type="$sanitizedInputType" $placeholderContent $requirement>
        HTML;     
    }

    function file_upload(string $id, array $acceptedFiletypes = ['.pdf', '.doc', '.docx', '.jpg', '.jpeg', '.png'], bool $required = true) {
        $sanitizedID = htmlspecialchars(normalize_identifier($id));

        $acceptedFiletypesAsString = count($acceptedFiletypes) > 0
            ? htmlspecialchars(implode(",", $acceptedFiletypes))
            : "*";
        
        $requirement = $required? "required" : "";

        return <<< HTML
            <div class="file-input-wrapper">
                <input type="file" id="$sanitizedID" name="$sanitizedID" accept="$acceptedFiletypesAsString" $requirement>
            </div>
        HTML;
    }
?>