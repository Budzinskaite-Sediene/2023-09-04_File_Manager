<?php

$dir = './';
$back_link = '';

if ( isset( $_GET[ 'dir' ] ) and $_GET[ 'dir' ] != '' ) {
    $dir = $_GET[ 'dir' ];

    $path_array = explode( '/', $dir );

    if ( $dir != './' ) {
        if ( count( $path_array ) > 1 ) {
            unset( $path_array[ count( $path_array ) - 1 ] );
            $back_link = implode( '/', $path_array );
        } else {
            $back_link = './';
        }
        $back_link = '<tr>
        <td colspan="2"><a href="?dir=' . $back_link . '"><i class="fa fa-arrow-left icon-color"></i></a></td>
    </tr>';
    }
}

//Failo pavadinimo keitimas
if ( isset( $_POST[ 'file_name_edited' ] ) AND $_POST[ 'file_name_edited' ] != '' ) {
    $file_path = explode( '/', $_GET[ 'edit' ] );
    unset( $file_path[ count( $file_path ) - 1 ] );
    $file_path[] = $_POST[ 'file_name_edited' ];

    $to = implode( '/', $file_path );

    //pavadinimo redagavimo eilutė panaudojant basename funkciją
    //$newFile = str_replace( basename( $_GET[ 'edit' ] ), $_POST[ 'file_name_edited' ], $_GET[ 'edit' ] );

    rename( $_GET[ 'edit' ], $to );

    header( 'Location: ?dir=' . $dir );
}

$data = scandir( $dir );

unset( $data[ 0 ] );
unset( $data[ 1 ] );

function getFileSize( $filePath ) {
    if ( file_exists( $filePath ) ) {
        $size = filesize( $filePath );
        return formatFileSize( $size );
    }
    return '-';
}

function formatFileSize( $bytes, $decimals = 2 ) {
    $size = [ 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB' ];
    $factor = floor( ( strlen( $bytes ) - 1 ) / 3 );
    return sprintf( "%.{$decimals}f", $bytes / ( 1024 ** $factor ) ) . ' ' . @$size[ $factor ];
}
?>

<!DOCTYPE html>
<html lang = 'en'>

<head>
<meta charset = 'UTF-8'>
<meta http-equiv = 'X-UA-Compatible' content = 'IE=edge'>
<meta name = 'viewport' content = 'width=device-width, initial-scale=1.0'>
<title>File Manager</title>
<link href = 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css' rel = 'stylesheet'>
<link rel = 'stylesheet' href = 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css'>
<link rel = 'stylesheet' href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css'>
<link rel = 'stylesheet' href = 'style.css'>
</head>
<body class = 'container mt-5'>
<h1>File manager</h1>
<div class = 'container py-4'>
<?php if ( isset( $_GET[ 'm' ] ) AND $_GET[ 'm' ] != '' ) {
    ?>
    <div class = 'alert alert-danger'>
    < ?= $_GET[ 'm' ] ?>
    </div>
    <?php }
    ?>
    <form method = 'POST' action = ''>
    <?php
    if ( isset( $_POST[ 'delete_selected' ] ) && isset( $_POST[ 'delete' ] ) ) {
        $files_to_delete = $_POST[ 'delete' ];
        foreach ( $files_to_delete as $file ) {
            if ( is_file( $file ) ) {
                unlink( $file );
                // Ištrinti failą
            } elseif ( is_dir( $file ) ) {
                rmdir( $file );
                // Ištrinti aplanką
            }
        }
        header( 'Location: ?dir=' . $dir );
    }
    ?>
    <div class = 'row mb-3'>
    <div class = 'col'>
    <div class = 'col'>
    <button type = 'button' class = 'btn btn-primary' id = 'select-all-checkboxes'>Select All</button>

    <button type = 'submit' class = 'btn btn-danger' name = 'delete_selected'>Delete selected</button>
    </div>

    </div>
    <table class = 'table'>
    <thead>
    <tr>
    <th>Name</th>
    <th>Size</th>
    <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    <!-- Atgalinės nuorodos atvaizdavimas -->
    <?php echo $back_link;
    ?>
    <!-- Failų ir folderių sąrašqas -->
    <?php
    foreach ( $data as $item ) {

        if ( $item === 'index.php' || $item === 'style.css' || $item === '.git' ) {
            continue;
        }

        $path = $dir . '/' . $item;

        if ( $dir === './' ) {
            $path = $item;
        }

        //Ikonų priskyrimas
        $icon = 'folder';

        $file_formats = [
            'pdf' => 'file-earmark-pdf',
            'txt' => 'filetype-txt',
            'exe' => 'filetype-exe',
            'css' => 'filetype-css',
            'js' => 'filetype-js',
            'json' => 'filetype-json',
            'jpg' => 'filetype-jpg',
            'png' => 'filetype-png',
            'gif' => 'filetype-gif',
            'csv' => 'filetype-csv',
            'php' => 'filetype-php'
        ];

        if ( !is_dir( $path ) ) {
            $icon = 'file-earmark';

            $filename = explode( '.', $item );
            $filename = $filename[ count( $filename ) - 1 ];

            if ( array_key_exists( $filename, $file_formats ) ) {
                $icon = $file_formats[ $filename ];
            }
        }
        if ( isset( $_GET[ 'download' ] ) ) {
            $file_path = $_GET[ 'download' ];

            if ( file_exists( $file_path ) ) {

                header( 'Content-Type: application/octet-stream' );
                header( 'Content-Disposition: attachment; filename="' . basename( $file_path ) . '"' );
                header( 'Content-Length: ' . filesize( $file_path ) );

                readfile( $file_path );

                exit;
            } else {
                echo 'File not found.';
            }
        }
        ?>
        <tr>
        <td>
        <div class = 'form-check'>
        <input type = 'checkbox' class = 'form-check-input' name = 'delete[]' value = "<?= $path ?>">
        <i class = "bi bi-<?= $icon ?>"></i>
        <!-- Failo pavadinimas -->
        <?php
        if ( is_dir( $path ) ) {
            echo '<a href="?dir=' . $path . '">' . $item . '</a>';
        } else {
            echo '<a href="'.$path.'" target="_blank">' . $item . '</a>';
        }
        ?>
        </td>
        <td>
        <!-- Failo dydis -->
        <?php
        if ( is_dir( $path ) ) {
            echo '-';
        } else {
            echo getFileSize( $path );
        }

        if (isset($_GET['delete_file']) && isset($_GET['dir'])) {
    $fileToDelete = $_GET['delete_file'];
    $dir = $_GET['dir'];
    
    if (file_exists($fileToDelete)) {
        unlink($fileToDelete);
        header('Location: ?dir=' . $dir);
        exit;
    }
}

if (isset($_GET['delete_folder']) && isset($_GET['dir'])) {
    $folderToDelete = $_GET['delete_folder'];
    $dir = $_GET['dir'];
    
    if (is_dir($folderToDelete)) {
        rmdir($folderToDelete);
        header('Location: ?dir=' . $dir);
        exit;
    }
}

        ?>
        </td>
        <td>
        <!-- Parsisiųsti failo ikona -->
        <?php if ( !is_dir( $path ) ) {
            ?>
            <a href = "?download=<?= $path ?>"><i class = 'fas fa-download icon-color'></i></a>
            <?php }
            ?>
            <!-- Redagavimo ikona -->
            <a href = "?edit=<?= $path ?>&dir=<?= $dir ?>"><i class = 'fas fa-edit icon-color'></i></a>
            <!-- Ištrinti ikona -->
          <?php if (is_dir($path)) {
        $delete_link = '?delete_folder=' . urlencode($path) . '&dir=' . urlencode($dir);
        $delete_confirm_message = 'Are you sure you want to delete this folder?';
    } else {
        $delete_link = '?delete_file=' . urlencode($path) . '&dir=' . urlencode($dir);
        $delete_confirm_message = 'Are you sure you want to delete this file?';
    } ?>
    <a href="<?= $delete_link ?>" onclick="return confirm('<?= $delete_confirm_message ?>')"><i class='fas fa-trash-alt icon-color'></i></a>
            </td>
            </tr>
            <?php }
            ?>
            </tbody>
            </table>
            <!-- Failo pavadinimo redagavimo forma -->
            <?php if ( isset( $_GET[ 'edit' ] ) ) {
                ?>
                <h2>Edit file name</h2>
                <form method = 'POST'>
                <!-- Jeigu norime gauti duomenis iš laukelio, tačiau šis neturi būti atvaizduojamas puslapyje, galime panaudoti type = 'hidden' variaciją -->
                <!-- <input type = 'hidden' name = 'file_name_original' class = 'form-control' value = "<?= $_GET['edit'] ?>" /> -->
                <div class = 'mb-3'>
                <label>New File Name</label>
                <input type = 'text' name = 'file_name_edited' class = 'form-control' />
                </div>
                <button class = 'btn btn-primary'>Submit</button>
                </form>
                <?php } else {
                    ?>
                    <?php }
                    ?>
                    </div>
                    <script>
                    document.addEventListener( 'DOMContentLoaded', function() {
                        const selectAllButton = document.getElementById( 'select-all-checkboxes' );
                        const checkboxes = document.querySelectorAll( 'input[type="checkbox"][name^="delete[]"]' );

                        selectAllButton.addEventListener( 'click', function() {
                            checkboxes.forEach( function( checkbox ) {
                                checkbox.checked = true;
                            }
                        );
                    }
                );
            }
        );
        document.addEventListener('DOMContentLoaded', function () {
    const selectAllButton = document.getElementById('select-all-checkboxes');
    const checkboxes = document.querySelectorAll('input[type="checkbox"][name^="delete[]"]');

    let allChecked = false;

    selectAllButton.addEventListener('click', function () {
        checkboxes.forEach(function (checkbox) {
            checkbox.checked = !allChecked; // Pakeičia checkbox būseną (pažymi/atžymi)
        });

        allChecked = !allChecked; // Pakeičia visų checkboxų būseną
    });
});
        </script>
        </body>
        </html>