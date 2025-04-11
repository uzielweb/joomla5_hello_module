<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Module generator</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@latest/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center h3">Module generator</h1>
        <h2 class="text-center h4">This will use mod_hello, replicating and changing contents to create a new customized module</h2>
        <form method="POST" class="mt-4">
            <div class="mb-3">
                <label for="moduleName" class="form-label">Module Name:</label>
                <input type="text" id="moduleName" name="moduleName" class="form-control" placeholder="Module Name" required>
            </div>

            <div class="mb-3">
                <label for="clientType" class="form-label">Client Type:</label>
                <select id="clientType" name="clientType" class="form-select" required>
                    <option value="site">Site (Frontend)</option>
                    <option value="administrator">Administrator (Backend)</option>
                </select>
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
            $moduleName = $_POST['moduleName'];
            $moduleNameNoLatin =  preg_replace('/[^A-Za-z0-9]/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $moduleName)); // Remove caracteres não latinos
            $newWord = strtolower(str_replace(' ', '', $moduleNameNoLatin)); // Remove espaços e converte para minúsculas
            $author = $_POST['author'];
            $authorEmail = $_POST['authorEmail'];
            $authorUrl = $_POST['authorUrl'];
            $creationDate = $_POST['creationDate'];
            $newLang = $_POST['newLang'];
            $clientType = $_POST['clientType']; // Novo parâmetro para o tipo de cliente
        
            if (!is_dir($dir)) {
                echo '<span class="text-danger">The directory "mod_hello" does not exist!</span>';
                exit;
            }
             // Função para limpar o diretório cache
    function clearCache($cacheDir) {
        if (is_dir($cacheDir)) {
            $files = scandir($cacheDir);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..') {
                    $filePath = "$cacheDir/$file";
                    if (is_dir($filePath)) {
                        // Remove o diretório recursivamente
                        array_map('unlink', glob("$filePath/*.*")); // Remove arquivos
                        clearCache($filePath); // Remove o diretório recursivamente
                        rmdir($filePath); // Remove o diretório vazio
                    } else {
                        unlink($filePath); // Remove o arquivo
                    }
                }
            }
        } else {
            mkdir($cacheDir, 0755, true); // Cria o diretório se não existir
        }
    }
            // Função para copiar diretórios recursivamente
            function copyDirectory($source, $destination) {
                if (!is_dir($source)) {
                    return false;
                }
                if (is_dir($destination)) {
                    // Remove existing files in the destination directory
                    $files = scandir($destination);
                    foreach ($files as $file) {
                        if ($file !== '.' && $file !== '..') {
                            $filePath = "$destination/$file";
                            if (is_dir($filePath)) {
                                array_map('unlink', glob("$filePath/*.*")); // Remove files
                                rmdir($filePath); // Remove the empty directory
                            } else {
                                unlink($filePath); // Remove the file
                            }
                        }
                    }
                } else {
                    mkdir($destination, 0755, true);
                }
                $files = scandir($source);
                foreach ($files as $file) {
                    if ($file !== '.' && $file !== '..') {
                        $srcPath = "$source/$file";
                        $destPath = "$destination/$file";
                        if (is_dir($srcPath)) {
                            copyDirectory($srcPath, $destPath);
                        } else {
                            copy($srcPath, $destPath);
                        }
                    }
                }
                return true;
            }

            // Função para substituir conteúdo nos arquivos
            function replaceContentInFiles($dir, $newWord, $moduleName, $author, $authorEmail, $authorUrl, $creationDate, $newLang, $clientType) {
                $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
                foreach ($iterator as $file) {
                    if ($file->isFile() && is_writable($file)) {
                        $content = file_get_contents($file);
                        $content = str_replace(
                            ["Hello", "HELLO", "hello"],
                            [ucfirst($newWord), strtoupper($newWord), strtolower($newWord)],
                            $content
                        );

                        if (pathinfo($file, PATHINFO_EXTENSION) === 'xml') {
                            // Atualiza o atributo client no XML para o tipo selecionado
                            $content = preg_replace(
                                '/<extension type="module" client="site" method="upgrade">/',
                                '<extension type="module" client="' . $clientType . '" method="upgrade">',
                                $content
                            );
                            
                            $content = preg_replace([
                                '/<author>.*?<\/author>/',
                                '/<creationDate>.*?<\/creationDate>/',
                                '/<copyright>.*?<\/copyright>/',
                                '/<authorEmail>.*?<\/authorEmail>/',
                                '/<authorUrl>.*?<\/authorUrl>/',
                            ], [
                                "<author>$author</author>",
                                "<creationDate>$creationDate</creationDate>",
                                "<copyright>(C) " . date('Y') . " $author</copyright>",      
                                "<authorEmail>$authorEmail</authorEmail>",
                                "<authorUrl>$authorUrl</authorUrl>",
                            ], $content);

                          
                        $newLanguageEntry = "\t<language tag=\"$newLang\">language/$newLang/mod_$newWord.ini</language>\n";
                        $newLanguageEntry .= "\t<language tag=\"$newLang\">language/$newLang/mod_$newWord.sys.ini</language>\n";
                        $content = preg_replace('/(<languages>)/s', '$1' . "\n" . $newLanguageEntry, $content);
                        }

                        if (pathinfo($file, PATHINFO_EXTENSION) === 'ini') {
                            $content = str_replace(
                                ["MOD_HELLO", "Hello"],
                                ["MOD_$newWord", ucfirst($newWord)],
                                $content
                            );
                            $content = str_replace(
                                ["MOD_$newWord", ucfirst($newWord)],
                                ["MOD_$newWord", ucfirst($moduleName)],
                                $content
                            );
                        }

                        // Atualiza os namespaces para o tipo de cliente correto
                        if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                            // Substitui o namespace Site pelo tipo de cliente selecionado
                            if ($clientType === 'administrator') {
                                $content = str_replace(
                                    'namespace Joomla\Module\\' . ucfirst($newWord) . '\Site',
                                    'namespace Joomla\Module\\' . ucfirst($newWord) . '\Administrator',
                                    $content
                                );
                            }
                            
                            // Atualiza o registro do HelperFactory no provider.php
                            if (basename($file) === 'provider.php') {
                                $clientNamespace = ucfirst($clientType === 'administrator' ? 'Administrator' : 'Site');
                                // Substitui a string de registro do HelperFactory para o namespace correto
                                $content = str_replace(
                                    'new HelperFactory(\'\\Joomla\\Module\\Hello\\Site\\Helper\')',
                                    'new HelperFactory(\'\\Joomla\\Module\\' . ucfirst($newWord) . '\\' . $clientNamespace . '\\Helper\')',
                                    $content
                                );
                            }
                        }

                        file_put_contents($file, $content);
                    }
                }
            }

            // Função para renomear arquivos e diretórios
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

                $baseName = basename($dir);
                $newBaseName = str_replace(
                    ["hello", "HELLO"],
                    [strtolower($newWord), strtoupper($newWord)],
                    $baseName
                );

                if ($baseName !== $newBaseName) {
                    $newBasePath = dirname($dir) . DIRECTORY_SEPARATOR . $newBaseName;
                    rename($dir, $newBasePath);
                    echo '<span class="text-success">Directory renamed to '.$newBaseName.'. </span>';
                }
            }

            // Função para adicionar um novo idioma
            function addLanguage($dir, $newLang) {
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

            // Limpa o diretório de cache
    $cacheDir = 'cache/generators';
    clearCache($cacheDir);

    // Copia o diretório antes de fazer qualquer modificação
    $newDir = $cacheDir . '/mod_' . $newWord;
    if (copyDirectory($dir, $newDir)) {
        echo '<span class="text-success">Directory "mod_hello" copied to '.$newDir.'. </span>';
        
        // Copia o arquivo de documentação para o novo módulo
        if (file_exists('module_documentation.md')) {
            // Lê o conteúdo do arquivo de documentação
            $docContent = file_get_contents('module_documentation.md');
            
            // Substitui placeholders na documentação
            $docContent = str_replace(
                ['mod_yourmodule', 'YourmoduleHelper', 'mod_seumodulo', 'SeumoduloHelper'],
                ['mod_' . $newWord, ucfirst($newWord) . 'Helper', 'mod_' . $newWord, ucfirst($newWord) . 'Helper'],
                $docContent
            );
            
            // Salva a documentação personalizada no novo módulo
            file_put_contents($newDir . '/README.md', $docContent);
            echo '<span class="text-success">Documentation (Markdown) added to the module. </span>';
        }
        
        // Copia o arquivo de documentação HTML para o novo módulo
        if (file_exists('module_documentation.html')) {
            // Lê o conteúdo do arquivo de documentação HTML
            $docHtmlContent = file_get_contents('module_documentation.html');
            
            // Substitui placeholders na documentação HTML
            $docHtmlContent = str_replace(
                ['mod_yourmodule', 'YourmoduleHelper', 'mod_seumodulo', 'SeumoduloHelper'],
                ['mod_' . $newWord, ucfirst($newWord) . 'Helper', 'mod_' . $newWord, ucfirst($newWord) . 'Helper'],
                $docHtmlContent
            );
            
            // Salva a documentação HTML personalizada no novo módulo
            file_put_contents($newDir . '/documentation.html', $docHtmlContent);
            echo '<span class="text-success">Documentation (HTML) added to the module. </span>';
        }
        
        addLanguage($newDir, $newLang);
        replaceContentInFiles($newDir, $newWord, $moduleName, $author, $authorEmail, $authorUrl, $creationDate, $newLang, $clientType);
        renameFilesAndDirectories($newDir, $newWord);
        echo '<span class="text-success">Process completed successfully! </span>';
        
        // Adiciona informação sobre o tipo de cliente
        echo '<span class="text-info">Module created for ' . ($clientType === 'administrator' ? 'Administrator (Backend)' : 'Site (Frontend)') . '. </span>';
        
        // zip the new module
        $zip = new ZipArchive();
        $zipFileName = $cacheDir . '/mod_' . $newWord . '.zip';
        if ($zip->open($zipFileName, ZipArchive::CREATE) === TRUE) {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($newDir),
                RecursiveIteratorIterator::SELF_FIRST
            );
            foreach ($files as $file) {
                $file = str_replace('\\', '/', $file);
                if (in_array(basename($file), ['.', '..'])) {
                    continue;
                }
                if (is_dir($file) === TRUE) {
                    $zip->addEmptyDir(str_replace($newDir . '/', '', $file . '/'));
                } else if (is_file($file) === TRUE) {
                    $zip->addFromString(str_replace($newDir . '/', '', $file), file_get_contents($file));
                }
            }
            $zip->close();
            echo '<span class="text-success">The new module has been zipped successfully! </span>';
            // zip link
            echo '<span><a href="'.$zipFileName.'" class="btn btn-primary" download>Download module <strong>' . $moduleName . '<strong></a></span>';
        } else {
            echo '<span class="text-danger">Failed to zip the new module!';
        }


    }
}
        ?>
    </div>
</body>
</html>
