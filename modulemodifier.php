<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personalizar Módulo Joomla 5</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@latest/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Replace, Rename, and Update Module Files</h1>

        <form method="POST" class="mt-4">
            <div class="mb-3">
                <label for="moduleName" class="form-label">Module Name:</label>
                <input type="text" id="moduleName" name="moduleName" class="form-control" placeholder="Module Name" required>
            </div>

            <div class="mb-3">
                <label for="author" class="form-label">Author Name:</label>
                <input type="text" id="author" name="author" class="form-control" placeholder="Author name" required>
            </div>

            <div class="mb-3">
                <label for="authorEmail" class="form-label">Author Email:</label>
                <input type="email" id="authorEmail" name="authorEmail" class="form-control" placeholder="Author email" required>
            </div>

            <div class="mb-3">
                <label for="authorUrl" class="form-label">Author URL:</label>
                <input type="url" id="authorUrl" name="authorUrl" class="form-control" placeholder="Author website URL" required>
            </div>

            <div class="mb-3">
                <label for="creationDate" class="form-label">Creation Date:</label>
                <input type="date" id="creationDate" name="creationDate" class="form-control" required value="<?= date('Y-m-d') ?>">
            </div>

            <div class="mb-3">
                <label for="newLang" class="form-label">New Language:</label>
                <input type="text" id="newLang" name="newLang" class="form-control" placeholder="New language" required value="pt-BR">
            </div>

            <button type="submit" class="btn btn-primary">Execute</button>
        </form>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dir = 'mod_hello';
            $ModuleName = $_POST['moduleName'];
            $newWord = strtolower(str_replace(' ', '', $ModuleName));
            $author = $_POST['author'];
            $authorEmail = $_POST['authorEmail'];
            $authorUrl = $_POST['authorUrl'];
            $creationDate = $_POST['creationDate'];
            $newLang = $_POST['newLang'];

            if (!is_dir($dir)) {
                echo "<p class='text-danger'>The directory 'mod_hello' does not exist!</p>";
                exit;
            }

            // Função para copiar diretórios recursivamente
            function copyDirectory($source, $destination) {
                if (!is_dir($source)) {
                    return false;
                }
                // Criar o diretório de destino se não existir
                if (!is_dir($destination)) {
                    mkdir($destination, 0755, true);
                }
                // Copiar os arquivos
                $files = scandir($source);
                foreach ($files as $file) {
                    if ($file !== '.' && $file !== '..') {
                        $srcPath = "$source/$file";
                        $destPath = "$destination/$file";
                        if (is_dir($srcPath)) {
                            copyDirectory($srcPath, $destPath); // Recursão para subdiretórios
                        } else {
                            copy($srcPath, $destPath);
                        }
                    }
                }
                return true;
            }

            // Atualiza este conteúdo para aceitar $ModuleName
            function replaceContentInFiles($dir, $newWord, $ModuleName, $author, $authorEmail, $authorUrl, $creationDate, $newLang) {
                $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
                foreach ($iterator as $file) {
                    if ($file->isFile() && is_writable($file)) {
                        $content = file_get_contents($file);

                        // Substituições no conteúdo
                        $content = str_replace(
                            ["Hello", "HELLO", "hello"],
                            [ucfirst($newWord), strtoupper($newWord), strtolower($newWord)],
                            $content
                        );

                        // Substituições específicas de XML
                        if (pathinfo($file, PATHINFO_EXTENSION) === 'xml') {
                            $content = preg_replace([
                                '/<author>.*?<\/author>/',
                                '/<creationDate>.*?<\/creationDate>/',
                                '/<copyright>.*?<\/copyright>/',
                                '/<authorEmail>.*?<\/authorEmail>/',
                                '/<authorUrl>.*?<\/authorUrl>/',
                            ], [
                                "<author>$author</author>",
                                "<creationDate>$creationDate</creationDate>",
                                "(C) " . date('Y') . " $author",
                                "<authorEmail>$authorEmail</authorEmail>",
                                "<authorUrl>$authorUrl</authorUrl>",
                            ], $content);

                            // Add new language entry
                            $newLanguageEntry = "\n\t<language tag=\"$newLang\">language/$newLang/mod_$newWord.ini</language>\n";
                            $content = preg_replace('/(<languages>.*?<\/languages>)/s', '$1' . $newLanguageEntry, $content);
                        }

                        // Atualiza traduções nos arquivos .ini
                        if (pathinfo($file, PATHINFO_EXTENSION) === 'ini') {
                            $content = str_replace(
                                ["MOD_HELLO", "Hello"],
                                ["MOD_$newWord", ucfirst($newWord)],
                                $content
                            );
                            $content = str_replace(
                                ["MOD_$newWord", ucfirst($newWord)],
                                ["MOD_$newWord", ucfirst($ModuleName)],
                                $content
                            );
                        }

                        file_put_contents($file, $content);
                    }
                }
            }

            /**
             * Renomeia arquivos e diretórios.
             */
            function renameFilesAndDirectories($dir, $newWord) {
                $iterator = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
                    RecursiveIteratorIterator::CHILD_FIRST
                );

                foreach ($iterator as $file) {
                    $newName = str_replace(
                        ["Hello", "HELLO", "hello"],
                        [ucfirst($newWord), strtoupper($newWord), strtolower($newWord)],
                        $file->getFilename()
                    );

                    $newPath = $file->getPath() . DIRECTORY_SEPARATOR . $newName;

                    if (is_writable($file)) {
                        rename($file->getPathname(), $newPath);
                    }
                }

                // Renomeia o diretório principal
                $baseName = basename($dir);
                $newBaseName = str_replace(
                    ["hello", "HELLO"],
                    [strtolower($newWord), strtoupper($newWord)],
                    $baseName
                );

                if ($baseName !== $newBaseName) {
                    $newBasePath = dirname($dir) . DIRECTORY_SEPARATOR . $newBaseName;
                    rename($dir, $newBasePath);
                    echo "<p class='text-success'>Directory renamed to '$newBaseName'</p>";
                }
            }

            // Adiciona o novo idioma
            function addLanguage($dir, $newLang) {
                // Copia a linguagem en-GB para a nova linguagem escolhida
                $source = $dir . '/language/en-GB';
                $dest = $dir . '/language/' . $newLang;
                if (!is_dir($dest)) {
                    mkdir($dest, 0755, true);
                }
                $files = scandir($source);
                foreach ($files as $file) {
                    if ($file != "." && $file != "..") {
                        copy($source . '/' . $file, $dest . '/' . $file);
                    }
                }
            }

            // Copia o diretório antes de fazer qualquer modificação
            $newDir = 'mod_' . $newWord;
            if (copyDirectory($dir, $newDir)) {
                echo "<p class='text-success'>Directory 'mod_hello' copied to '$newDir'</p>";
                addLanguage($newDir, $newLang);
                replaceContentInFiles($newDir, $newWord, $ModuleName, $author, $authorEmail, $authorUrl, $creationDate, $newLang);
                renameFilesAndDirectories($newDir, $newWord);
                echo "<p class='text-success'>Process completed successfully!</p>";
            } else {
                echo "<p class='text-danger'>Failed to copy directory!</p>";
            }
        }
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap
