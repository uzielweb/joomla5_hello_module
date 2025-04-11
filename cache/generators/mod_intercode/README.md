# Joomla Module Documentation

## English

### Installation

1. Log in to your Joomla administrator panel
1. Go to System > Install > Extensions
1. Upload the module ZIP file
1. Go to System > Manage > Extensions > Modules
1. Find your newly installed module and click on it to configure

### Customizing the Template (default.php)

The template file is located at `mod_intercode/tmpl/default.php`. You can modify this file to change how your module appears on the frontend or backend.

```php
<?php
// No direct access
defined('_JEXEC') or die;

// Access module parameters
$greeting = $params->get('greeting', 'Intercode, World!');

// Access data from the helper
if (!$list) {
    return;
}
?>

<div class="custom-module">
    <h3><?php echo $greeting; ?></h3>
    <ul>
        <?php foreach ($list as $item) : ?>
            <li><?php echo $item->title; ?></li>
        <?php endforeach; ?>
    </ul>
</div>
```

### Modifying the Helper (IntercodeHelper.php)

The helper file is located at `mod_intercode/src/Helper/IntercodeHelper.php`. You can modify the `getItems` method to change what data is retrieved and passed to the template.

```php
public function getItems(Registry $params, SiteApplication $app)
{
    // Get database connection
    $db = $this->getDatabase();
    
    // Get current user
    $user = $app->getIdentity();
    
    // Build your custom query
    $query = $db->getQuery(true)
        ->select('a.id, a.title, a.alias')  // Select additional fields
        ->from('#__content AS a')           // Query articles instead of modules
        ->where('a.state = 1')              // Only published articles
        ->order('a.created DESC')           // Order by creation date
        ->setLimit($params->get('count', 5)); // Limit results
        
    // Add category filter if specified in parameters
    $categoryId = $params->get('category_id');
    if ($categoryId) {
        $query->where('a.catid = ' . (int) $categoryId);
    }
    
    $db->setQuery($query);
    $items = $db->loadObjectList();
    
    return $items;
}
```

### Adding Custom CSS

There are two ways to add custom CSS to your module:

1. **Before installation**:
   - Edit the CSS file located at `mod_intercode/media/css/style.css`
   - This will be included in the installation package

2. **After installation**:
   - The CSS file is moved to Joomla's media directory: `/media/mod_intercode/css/style.css`
   - You can edit this file directly, or preferably:
   - Create a custom CSS file in your template's CSS directory and load it in your template

```css
.custom-module {
    background-color: #f5f5f5;
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 20px;
}

.custom-module h3 {
    color: #333;
    border-bottom: 1px solid #ddd;
    padding-bottom: 10px;
}

.custom-module ul {
    list-style-type: none;
    padding-left: 0;
}

.custom-module li {
    padding: 5px 0;
    border-bottom: 1px dotted #eee;
}
```

### Adding Custom JavaScript

There are two ways to add custom JavaScript to your module:

1. **Before installation**:
   - Edit the JavaScript file located at `mod_intercode/media/js/script.js`
   - This will be included in the installation package

2. **After installation**:
   - The JS file is moved to Joomla's media directory: `/media/mod_intercode/js/script.js`
   - You can edit this file directly, or preferably:
   - Create a custom JS file in your template's JS directory and load it in your template

```javascript
document.addEventListener('DOMContentLoaded', function() {
    // Get all instances of your module
    var modules = document.querySelectorAll('.custom-module');
    
    modules.forEach(function(module) {
        // Add click events to list items
        var items = module.querySelectorAll('li');
        items.forEach(function(item) {
            item.addEventListener('click', function() {
                console.log('Clicked:', this.textContent);
                // Add your custom functionality here
            });
        });
    });
});
```

### Using Translatable Strings in JavaScript

To make your JavaScript code support multiple languages, you can use Joomla's translation system:

1. **Define your strings in language files**:
   - Add your strings to the language files (`mod_intercode.ini`)
   
   ```ini
   MOD_YOURMODULE_JS_ALERT="This is a translatable message"
   MOD_YOURMODULE_JS_CONFIRM="Are you sure you want to continue?"
   ```

2. **Pass the strings to JavaScript**:
   - In your template file (`default.php`), add:
   
   ```php
   $wa = $app->getDocument()->getWebAssetManager();
   
   // Add translatable strings to JavaScript
   $wa->addInlineScript('
       Joomla.Text._.MOD_' . strtoupper($module->name) . '_JS_ALERT = "' . Text::_('MOD_' . strtoupper($module->name) . '_JS_ALERT') . '";
       Joomla.Text._.MOD_' . strtoupper($module->name) . '_JS_CONFIRM = "' . Text::_('MOD_' . strtoupper($module->name) . '_JS_CONFIRM') . '";
   ');
   ```

3. **Use the strings in your JavaScript**:
   
   ```javascript
   document.addEventListener('DOMContentLoaded', function() {
       var modules = document.querySelectorAll('.custom-module');
       
       modules.forEach(function(module) {
           var items = module.querySelectorAll('li');
           items.forEach(function(item) {
               item.addEventListener('click', function() {
                   // Use the translatable string
                   alert(Joomla.Text._('MOD_YOURMODULE_JS_ALERT'));
                   
                   // Use with parameters
                   var message = Joomla.Text._('MOD_YOURMODULE_JS_CONFIRM').replace('%s', this.textContent);
                   if (confirm(message)) {
                       console.log('Confirmed!');
                   }
               });
           });
       });
   });
   ```

### Modifying the XML File

The module's XML file (`mod_intercode.xml`) defines the metadata, files, parameters, and dependencies of the module. You can modify it to add new parameters or functionality:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<extension type="module" client="site" method="upgrade">
    <name>mod_intercode</name>
    <author>Your Name</author>
    <creationDate>2023-04-11</creationDate>
    <copyright>(C) 2023 Your Name</copyright>
    <license>GNU General Public License version 2 or later; see LICENSE.txt</license>
    <authorEmail>your@email.com</authorEmail>
    <authorUrl>https://www.yoursite.com</authorUrl>
    <version>1.0.0</version>
    <description>MOD_YOURMODULE_XML_DESCRIPTION</description>
    <namespace path="src">Joomla\Module\Yourmodule</namespace>
    
    <!-- Files included in the module -->
    <files>
        <folder module="mod_intercode">services</folder>
        <folder>src</folder>
        <folder>tmpl</folder>
        <filename>documentation.html</filename>
    </files>
    
    <!-- Language files -->
    <languages>
        <language tag="en-GB">language/en-GB/mod_intercode.ini</language>
        <language tag="en-GB">language/en-GB/mod_intercode.sys.ini</language>
        <language tag="pt-BR">language/pt-BR/mod_intercode.ini</language>
        <language tag="pt-BR">language/pt-BR/mod_intercode.sys.ini</language>
    </languages>
    
    <!-- Media files -->
    <media destination="mod_intercode" folder="media">
        <folder>images</folder>
        <folder>css</folder>
        <folder>js</folder>
    </media>
    
    <!-- Module configuration -->
    <config>
        <fields name="params" addfieldprefix="Joomla\Component\Content\Administrator\Field">
            <!-- Basic parameters -->
            <fieldset name="basic">
                <field name="greeting" type="text" 
                    label="MOD_YOURMODULE_GREETING_LABEL" 
                    description="MOD_YOURMODULE_GREETING_DESC" 
                    default="Intercode, World!" />
                    
                <field name="show_date" type="radio" 
                    label="MOD_YOURMODULE_SHOW_DATE_LABEL" 
                    description="MOD_YOURMODULE_SHOW_DATE_DESC" 
                    default="1" 
                    class="btn-group btn-group-yesno">
                    <option value="1">JYES</option>
                    <option value="0">JNO</option>
                </field>
                
                <field name="date_format" type="text" 
                    label="MOD_YOURMODULE_DATE_FORMAT_LABEL" 
                    description="MOD_YOURMODULE_DATE_FORMAT_DESC" 
                    default="d/m/Y" 
                    showon="show_date:1" />
            </fieldset>
            
            <!-- Advanced parameters -->
            <fieldset name="advanced">
                <field name="layout" type="modulelayout" 
                    label="JFIELD_ALT_LAYOUT_LABEL" 
                    class="form-select" 
                    validate="moduleLayout" />

                <field name="moduleclass_sfx" type="textarea" 
                    label="COM_MODULES_FIELD_MODULECLASS_SFX_LABEL" 
                    rows="3" 
                    validate="CssIdentifier" />

                <field name="cache" type="list" 
                    label="COM_MODULES_FIELD_CACHING_LABEL" 
                    default="1" 
                    filter="integer" 
                    validate="options">
                    <option value="1">JGLOBAL_USE_GLOBAL</option>
                    <option value="0">COM_MODULES_FIELD_VALUE_NOCACHING</option>
                </field>

                <field name="cache_time" type="number" 
                    label="COM_MODULES_FIELD_CACHE_TIME_LABEL" 
                    default="900" 
                    filter="integer" />
            </fieldset>
        </fields>
    </config>
</extension>
```

### Adding Inline Subforms

Subforms allow you to create complex and repeatable fields in your module. Here's how to implement them:

1. **Add the subform field in the XML file**:

```xml
<fieldset name="basic">
    <!-- Other fields here -->
    
    <field name="items" type="subform" 
        label="MOD_YOURMODULE_ITEMS_LABEL"
        description="MOD_YOURMODULE_ITEMS_DESC"
        multiple="true"
        min="1"
        max="10"
        layout="joomla.form.field.subform.repeatable-table">
        <form>
            <field name="title" type="text" 
                label="MOD_YOURMODULE_ITEM_TITLE_LABEL" 
                description="MOD_YOURMODULE_ITEM_TITLE_DESC" />
                
            <field name="description" type="textarea" 
                label="MOD_YOURMODULE_ITEM_DESC_LABEL" 
                description="MOD_YOURMODULE_ITEM_DESC_DESC" 
                rows="3" />
                
            <field name="image" type="media" 
                label="MOD_YOURMODULE_ITEM_IMAGE_LABEL" 
                description="MOD_YOURMODULE_ITEM_IMAGE_DESC" />
                
            <field name="link" type="url" 
                label="MOD_YOURMODULE_ITEM_LINK_LABEL" 
                description="MOD_YOURMODULE_ITEM_LINK_DESC" 
                filter="url" />
                
            <field name="target" type="list" 
                label="MOD_YOURMODULE_ITEM_TARGET_LABEL" 
                description="MOD_YOURMODULE_ITEM_TARGET_DESC" 
                default="_self">
                <option value="_self">MOD_YOURMODULE_ITEM_TARGET_SELF</option>
                <option value="_blank">MOD_YOURMODULE_ITEM_TARGET_BLANK</option>
            </field>
        </form>
    </field>
</fieldset>
```

2. **Add translation strings in the language file**:

```ini
; Subform Items
MOD_YOURMODULE_ITEMS_LABEL="Items"
MOD_YOURMODULE_ITEMS_DESC="Add items to your module"
MOD_YOURMODULE_ITEM_TITLE_LABEL="Title"
MOD_YOURMODULE_ITEM_TITLE_DESC="Item title"
MOD_YOURMODULE_ITEM_DESC_LABEL="Description"
MOD_YOURMODULE_ITEM_DESC_DESC="Item description"
MOD_YOURMODULE_ITEM_IMAGE_LABEL="Image"
MOD_YOURMODULE_ITEM_IMAGE_DESC="Item image"
MOD_YOURMODULE_ITEM_LINK_LABEL="Link"
MOD_YOURMODULE_ITEM_LINK_DESC="Link to the item"
MOD_YOURMODULE_ITEM_TARGET_LABEL="Link Target"
MOD_YOURMODULE_ITEM_TARGET_DESC="How the link will be opened"
MOD_YOURMODULE_ITEM_TARGET_SELF="Same Window"
MOD_YOURMODULE_ITEM_TARGET_BLANK="New Window"
```

3. **Display the subform items in the template (default.php)**:

```php
<?php
// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

// Load styles and scripts
$wa = $app->getDocument()->getWebAssetManager();
$wa->registerAndUseStyle('mod_intercode_css', 'media/mod_intercode/css/style.css');
$wa->registerAndUseScript('mod_intercode_script', 'media/mod_intercode/js/script.js');

// Get the subform items
$items = $params->get('items', []);

// Check if there are items to display
if (empty($items)) {
    return;
}
?>

<div class="mod-yourmodule <?php echo $moduleclass_sfx; ?>">
    <h3><?php echo $params->get('greeting'); ?></h3>
    
    <?php if ($params->get('show_date', 1)) : ?>
        <div class="mod-yourmodule__date">
            <?php echo HTMLHelper::_('date', 'now', $params->get('date_format', 'd/m/Y')); ?>
        </div>
    <?php endif; ?>
    
    <div class="mod-yourmodule__items">
        <?php foreach ($items as $item) : ?>
            <div class="mod-yourmodule__item">
                <?php if (!empty($item->image)) : ?>
                    <div class="mod-yourmodule__item-image">
                        <?php if (!empty($item->link)) : ?>
                            <a href="<?php echo $item->link; ?>" target="<?php echo $item->target; ?>">
                                <img src="<?php echo Uri::root() . $item->image; ?>" alt="<?php echo $item->title; ?>">
                            </a>
                        <?php else : ?>
                            <img src="<?php echo Uri::root() . $item->image; ?>" alt="<?php echo $item->title; ?>">
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <div class="mod-yourmodule__item-content">
                    <?php if (!empty($item->title)) : ?>
                        <h4 class="mod-yourmodule__item-title">
                            <?php if (!empty($item->link)) : ?>
                                <a href="<?php echo $item->link; ?>" target="<?php echo $item->target; ?>">
                                    <?php echo $item->title; ?>
                                </a>
                            <?php else : ?>
                                <?php echo $item->title; ?>
                            <?php endif; ?>
                        </h4>
                    <?php endif; ?>
                    
                    <?php if (!empty($item->description)) : ?>
                        <div class="mod-yourmodule__item-description">
                            <?php echo $item->description; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
```

4. **Process the subform items in the Helper**:

You can also process the subform items in the Helper to add additional logic:

```php
public function getItems(Registry $params, SiteApplication $app)
{
    // Get the subform items
    $rawItems = $params->get('items', []);
    $processedItems = [];
    
    // Process each item
    foreach ($rawItems as $item) {
        // Create an object for the processed item
        $processedItem = new \stdClass();
        
        // Copy basic properties
        $processedItem->title = $item->title;
        $processedItem->description = $item->description;
        $processedItem->image = $item->image;
        $processedItem->link = $item->link;
        $processedItem->target = $item->target;
        
        // Add additional properties or process data
        if (!empty($item->link)) {
            // Check if the link is internal or external
            $processedItem->is_external = (strpos($item->link, 'http') === 0);
            
            // Add an icon based on the link type
            $processedItem->icon = $processedItem->is_external ? 'external-link' : 'link';
        }
        
        // Add the processed item to the list
        $processedItems[] = $processedItem;
    }
    
    return $processedItems;
}
```

5. **Use the processed items in the template**:

```php
<?php
// No direct access
defined('_JEXEC') or die;

// Check if there are items
if (empty($list)) {
    return;
}
?>

<div class="mod-yourmodule <?php echo $moduleclass_sfx; ?>">
    <h3><?php echo $params->get('greeting'); ?></h3>
    
    <div class="mod-yourmodule__items">
        <?php foreach ($list as $item) : ?>
            <div class="mod-yourmodule__item">
                <!-- Item content -->
                <h4>
                    <?php if (!empty($item->link)) : ?>
                        <a href="<?php echo $item->link; ?>" target="<?php echo $item->target; ?>">
                            <?php echo $item->title; ?>
                            <span class="icon-<?php echo $item->icon; ?>"></span>
                        </a>
                    <?php else : ?>
                        <?php echo $item->title; ?>
                    <?php endif; ?>
                </h4>
                
                <!-- Rest of the content -->
            </div>
        <?php endforeach; ?>
    </div>
</div>
```

## Official Joomla Resources for Developers

For more information on Joomla development, see the following official resources:

- [Joomla Developer Documentation](https://manual.joomla.org/docs/get-started/)
- [Module Development Guide](https://manual.joomla.org/docs/building-extensions/modules/module-development-tutorial/)
- [Joomla API Reference](https://api.joomla.org/)
- [Joomla Coding Standards](https://manual.joomla.org/docs/get-started/codestyle/)
- [Joomla Developer Forum](https://forum.joomla.org/viewforum.php?f=706)
- [Joomla GitHub](https://github.com/joomla/joomla-cms)
- [Joomla Official Manual](https://manual.joomla.org/)

---

# Documentação do Módulo - Português

## Instalação

Para instalar o módulo:

1. Faça o download do arquivo ZIP do módulo
2. No painel de administração do Joomla, vá para Extensões > Gerenciar > Instalar
3. Arraste e solte o arquivo ZIP na área de upload ou use o botão "Procurar" para selecionar o arquivo
4. Clique em "Instalar"
5. Vá para Extensões > Módulos
6. Localize o módulo recém-instalado e clique nele para configurá-lo
7. Ative o módulo e atribua-o a uma posição
8. Salve as configurações

## Personalização do Template

O arquivo de template principal do módulo é `tmpl/default.php`. Você pode modificá-lo para alterar a aparência do módulo:

```php
<?php
// Sem acesso direto
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

// Acesse os parâmetros do módulo
$greeting = $params->get('greeting', 'Olá, Mundo!');
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx', ''));

// Acesse os itens do helper
$items = $list;
?>

<div class="mod-intercode<?php echo $moduleclass_sfx; ?>">
    <h3><?php echo $greeting; ?></h3>
    
    <?php if (!empty($items)) : ?>
        <ul>
            <?php foreach ($items as $item) : ?>
                <li><?php echo $item->title; ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else : ?>
        <p><?php echo Text::_('MOD_INTERCODE_NO_ITEMS'); ?></p>
    <?php endif; ?>
</div>
```

## Modificando o Helper

O arquivo Helper (`src/Helper/IntercodeHelper.php`) contém a lógica para recuperar dados para o módulo:

```php
<?php
namespace Joomla\Module\Intercode\Site\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Registry\Registry;
use Joomla\CMS\Application\SiteApplication;
use Joomla\Database\DatabaseInterface;

class IntercodeHelper
{
    private DatabaseInterface $db;
    
    public function __construct(DatabaseInterface $db)
    {
        $this->db = $db;
    }
    
    public function getItems(Registry $params, SiteApplication $app)
    {
        $db = $this->db;
        $query = $db->getQuery(true)
            ->select('a.id, a.title')
            ->from('#__modules AS a')
            ->order('a.title ASC')
            ->setLimit($params->get('count', 5));
        $db->setQuery($query);
        $items = $db->loadObjectList();
        return $items;
    }
}
```

Você pode modificar este arquivo para alterar como os dados são recuperados ou adicionar novas funcionalidades.

## Adicionando CSS e JavaScript

Você pode adicionar arquivos CSS e JavaScript personalizados ao seu módulo:

1. Coloque seus arquivos CSS em `media/css/`
2. Coloque seus arquivos JavaScript em `media/js/`
3. Registre e carregue os arquivos no template:

```php
// No arquivo default.php
$wa = $app->getDocument()->getWebAssetManager();
$wa->registerAndUseStyle('mod_intercode_css', 'media/mod_intercode/css/style.css');
$wa->registerAndUseScript('mod_intercode_script', 'media/mod_intercode/js/script.js');
```

Nota: Após a instalação do módulo, os arquivos de mídia são movidos para o diretório de mídia do Joomla (`/media/mod_intercode/`).

## Usando Strings Traduzíveis em JavaScript

Para fazer seu código JavaScript suportar múltiplos idiomas, você pode usar o sistema de tradução do Joomla:

1. **Defina suas strings nos arquivos de idioma**:
   - Adicione suas strings aos arquivos de idioma (`mod_intercode.ini`)
   
   ```ini
   MOD_INTERCODE_JS_ALERT="Esta é uma mensagem traduzível"
   MOD_INTERCODE_JS_CONFIRM="Tem certeza que deseja continuar?"
   ```

2. **Passe as strings para o JavaScript**:
   - No seu arquivo de template (`default.php`), adicione:

   ```php
   $wa = $app->getDocument()->getWebAssetManager();

   // Adiciona strings traduzíveis ao JavaScript
   $wa->addInlineScript('
       Joomla.Text._.MOD_' . strtoupper($module->name) . '_JS_ALERT = "' . Text::_('MOD_' . strtoupper($module->name) . '_JS_ALERT') . '";
       Joomla.Text._.MOD_' . strtoupper($module->name) . '_JS_CONFIRM = "' . Text::_('MOD_' . strtoupper($module->name) . '_JS_CONFIRM') . '";
   ');
   ```

3. **Use as strings no seu JavaScript**:

   ```javascript
   document.addEventListener('DOMContentLoaded', function() {
       var modules = document.querySelectorAll('.custom-module');
       
       modules.forEach(function(module) {
           var items = module.querySelectorAll('li');
           items.forEach(function(item) {
               item.addEventListener('click', function() {
                   // Use a string traduzível
                   alert(Joomla.Text._('MOD_INTERCODE_JS_ALERT'));
                   
                   // Use com parâmetros
                   var message = Joomla.Text._('MOD_INTERCODE_JS_CONFIRM').replace('%s', this.textContent);
                   if (confirm(message)) {
                       console.log('Confirmado!');
                   }
               });
           });
       });
   });
   ```

## Modificando o Arquivo XML

O arquivo XML do módulo (`mod_intercode.xml`) define os metadados, arquivos, parâmetros e dependências do módulo. Você pode modificá-lo para adicionar novos parâmetros ou funcionalidades:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<extension type="module" client="site" method="upgrade">
    <name>mod_intercode</name>
    <author>Seu Nome</author>
    <creationDate>2023-04-11</creationDate>
    <copyright>(C) 2023 Seu Nome</copyright>
    <license>GNU General Public License version 2 or later; see LICENSE.txt</license>
    <authorEmail>seu@email.com</authorEmail>
    <authorUrl>https://www.seusite.com</authorUrl>
    <version>1.0.0</version>
    <description>MOD_INTERCODE_XML_DESCRIPTION</description>
    <namespace path="src">Joomla\Module\Intercode</namespace>
    
    <!-- Arquivos incluídos no módulo -->
    <files>
        <folder module="mod_intercode">services</folder>
        <folder>src</folder>
        <folder>tmpl</folder>
        <filename>documentation.html</filename>
    </files>
    
    <!-- Arquivos de idioma -->
    <languages>
        <language tag="en-GB">language/en-GB/mod_intercode.ini</language>
        <language tag="en-GB">language/en-GB/mod_intercode.sys.ini</language>
        <language tag="pt-BR">language/pt-BR/mod_intercode.ini</language>
        <language tag="pt-BR">language/pt-BR/mod_intercode.sys.ini</language>
    </languages>
    
    <!-- Arquivos de mídia -->
    <media destination="mod_intercode" folder="media">
        <folder>images</folder>
        <folder>css</folder>
        <folder>js</folder>
    </media>
    
    <!-- Configuração do módulo -->
    <config>
        <fields name="params" addfieldprefix="Joomla\Component\Content\Administrator\Field">
            <!-- Parâmetros básicos -->
            <fieldset name="basic">
                <field name="greeting" type="text" 
                    label="MOD_INTERCODE_GREETING_LABEL" 
                    description="MOD_INTERCODE_GREETING_DESC" 
                    default="Olá, Mundo!" />
                    
                <field name="show_date" type="radio" 
                    label="MOD_INTERCODE_SHOW_DATE_LABEL" 
                    description="MOD_INTERCODE_SHOW_DATE_DESC" 
                    default="1" 
                    class="btn-group btn-group-yesno">
                    <option value="1">JYES</option>
                    <option value="0">JNO</option>
                </field>
                
                <field name="date_format" type="text" 
                    label="MOD_INTERCODE_DATE_FORMAT_LABEL" 
                    description="MOD_INTERCODE_DATE_FORMAT_DESC" 
                    default="d/m/Y" 
                    showon="show_date:1" />
            </fieldset>
            
            <!-- Parâmetros avançados -->
            <fieldset name="advanced">
                <field name="layout" type="modulelayout" 
                    label="JFIELD_ALT_LAYOUT_LABEL" 
                    class="form-select" 
                    validate="moduleLayout" />

                <field name="moduleclass_sfx" type="textarea" 
                    label="COM_MODULES_FIELD_MODULECLASS_SFX_LABEL" 
                    rows="3" 
                    validate="CssIdentifier" />

                <field name="cache" type="list" 
                    label="COM_MODULES_FIELD_CACHING_LABEL" 
                    default="1" 
                    filter="integer" 
                    validate="options">
                    <option value="1">JGLOBAL_USE_GLOBAL</option>
                    <option value="0">COM_MODULES_FIELD_VALUE_NOCACHING</option>
                </field>

                <field name="cache_time" type="number" 
                    label="COM_MODULES_FIELD_CACHE_TIME_LABEL" 
                    default="900" 
                    filter="integer" />
            </fieldset>
        </fields>
    </config>
</extension>
```

## Adicionando Subforms Inline

Subforms permitem criar campos complexos e repetíveis em seu módulo. Veja como implementá-los:

1. **Adicione o campo subform no arquivo XML**:

```xml
<fieldset name="basic">
    <!-- Outros campos aqui -->
    
    <field name="items" type="subform" 
        label="MOD_INTERCODE_ITEMS_LABEL"
        description="MOD_INTERCODE_ITEMS_DESC"
        multiple="true"
        min="1"
        max="10"
        layout="joomla.form.field.subform.repeatable-table">
        <form>
            <field name="title" type="text" 
                label="MOD_INTERCODE_ITEM_TITLE_LABEL" 
                description="MOD_INTERCODE_ITEM_TITLE_DESC" />
                
            <field name="description" type="textarea" 
                label="MOD_INTERCODE_ITEM_DESC_LABEL" 
                description="MOD_INTERCODE_ITEM_DESC_DESC" 
                rows="3" />
                
            <field name="image" type="media" 
                label="MOD_INTERCODE_ITEM_IMAGE_LABEL" 
                description="MOD_INTERCODE_ITEM_IMAGE_DESC" />
                
            <field name="link" type="url" 
                label="MOD_INTERCODE_ITEM_LINK_LABEL" 
                description="MOD_INTERCODE_ITEM_LINK_DESC" 
                filter="url" />
                
            <field name="target" type="list" 
                label="MOD_INTERCODE_ITEM_TARGET_LABEL" 
                description="MOD_INTERCODE_ITEM_TARGET_DESC" 
                default="_self">
                <option value="_self">MOD_INTERCODE_ITEM_TARGET_SELF</option>
                <option value="_blank">MOD_INTERCODE_ITEM_TARGET_BLANK</option>
            </field>
        </form>
    </field>
</fieldset>
```

2. **Adicione strings de tradução no arquivo de idioma**:

```ini
; Itens do Subform
MOD_INTERCODE_ITEMS_LABEL="Itens"
MOD_INTERCODE_ITEMS_DESC="Adicione itens ao seu módulo"
MOD_INTERCODE_ITEM_TITLE_LABEL="Título"
MOD_INTERCODE_ITEM_TITLE_DESC="Título do item"
MOD_INTERCODE_ITEM_DESC_LABEL="Descrição"
MOD_INTERCODE_ITEM_DESC_DESC="Descrição do item"
MOD_INTERCODE_ITEM_IMAGE_LABEL="Imagem"
MOD_INTERCODE_ITEM_IMAGE_DESC="Imagem do item"
MOD_INTERCODE_ITEM_LINK_LABEL="Link"
MOD_INTERCODE_ITEM_LINK_DESC="Link para o item"
MOD_INTERCODE_ITEM_TARGET_LABEL="Destino do Link"
MOD_INTERCODE_ITEM_TARGET_DESC="Como o link será aberto"
MOD_INTERCODE_ITEM_TARGET_SELF="Mesma Janela"
MOD_INTERCODE_ITEM_TARGET_BLANK="Nova Janela"
```

3. **Exiba os itens do subform no template (default.php)**:

```php
<?php
// Sem acesso direto
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

// Carrega estilos e scripts
$wa = $app->getDocument()->getWebAssetManager();
$wa->registerAndUseStyle('mod_intercode_css', 'media/mod_intercode/css/style.css');
$wa->registerAndUseScript('mod_intercode_script', 'media/mod_intercode/js/script.js');

// Obtém os itens do subform
$items = $params->get('items', []);

// Verifica se há itens para exibir
if (empty($items)) {
    return;
}
?>

<div class="mod-intercode <?php echo $moduleclass_sfx; ?>">
    <h3><?php echo $params->get('greeting'); ?></h3>
    
    <?php if ($params->get('show_date', 1)) : ?>
        <div class="mod-intercode__date">
            <?php echo HTMLHelper::_('date', 'now', $params->get('date_format', 'd/m/Y')); ?>
        </div>
    <?php endif; ?>
    
    <div class="mod-intercode__items">
        <?php foreach ($items as $item) : ?>
            <div class="mod-intercode__item">
                <?php if (!empty($item->image)) : ?>
                    <div class="mod-intercode__item-image">
                        <?php if (!empty($item->link)) : ?>
                            <a href="<?php echo $item->link; ?>" target="<?php echo $item->target; ?>">
                                <img src="<?php echo Uri::root() . $item->image; ?>" alt="<?php echo $item->title; ?>">
                            </a>
                        <?php else : ?>
                            <img src="<?php echo Uri::root() . $item->image; ?>" alt="<?php echo $item->title; ?>">
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <div class="mod-intercode__item-content">
                    <?php if (!empty($item->title)) : ?>
                        <h4 class="mod-intercode__item-title">
                            <?php if (!empty($item->link)) : ?>
                                <a href="<?php echo $item->link; ?>" target="<?php echo $item->target; ?>">
                                    <?php echo $item->title; ?>
                                </a>
                            <?php else : ?>
                                <?php echo $item->title; ?>
                            <?php endif; ?>
                        </h4>
                    <?php endif; ?>
                    
                    <?php if (!empty($item->description)) : ?>
                        <div class="mod-intercode__item-description">
                            <?php echo $item->description; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
```

4. **Processe os itens do subform no Helper**:

Você também pode processar os itens do subform no Helper para adicionar lógica adicional:

```php
public function getItems(Registry $params, SiteApplication $app)
{
    // Obtém os itens do subform
    $rawItems = $params->get('items', []);
    $processedItems = [];
    
    // Processa cada item
    foreach ($rawItems as $item) {
        // Cria um objeto para o item processado
        $processedItem = new \stdClass();
        
        // Copia propriedades básicas
        $processedItem->title = $item->title;
        $processedItem->description = $item->description;
        $processedItem->image = $item->image;
        $processedItem->link = $item->link;
        $processedItem->target = $item->target;
        
        // Adiciona propriedades adicionais ou processa dados
        if (!empty($item->link)) {
            // Verifica se o link é interno ou externo
            $processedItem->is_external = (strpos($item->link, 'http') === 0);
            
            // Adiciona um ícone com base no tipo de link
            $processedItem->icon = $processedItem->is_external ? 'external-link' : 'link';
        }
        
        // Adiciona o item processado à lista
        $processedItems[] = $processedItem;
    }
    
    return $processedItems;
}
```

5. **Use os itens processados no template**:

```php
<?php
// Sem acesso direto
defined('_JEXEC') or die;

// Verifica se há itens
if (empty($list)) {
    return;
}
?>

<div class="mod-intercode <?php echo $moduleclass_sfx; ?>">
    <h3><?php echo $params->get('greeting'); ?></h3>
    
    <div class="mod-intercode__items">
        <?php foreach ($list as $item) : ?>
            <div class="mod-intercode__item">
                <!-- Conteúdo do item -->
                <h4>
                    <?php if (!empty($item->link)) : ?>
                        <a href="<?php echo $item->link; ?>" target="<?php echo $item->target; ?>">
                            <?php echo $item->title; ?>
                            <span class="icon-<?php echo $item->icon; ?>"></span>
                        </a>
                    <?php else : ?>
                        <?php echo $item->title; ?>
                    <?php endif; ?>
                </h4>
                
                <!-- Resto do conteúdo -->
            </div>
        <?php endforeach; ?>
    </div>
</div>
```

## Recursos Oficiais do Joomla para Desenvolvedores

Para mais informações sobre desenvolvimento Joomla, consulte os seguintes recursos oficiais:

- [Documentação para Desenvolvedores Joomla](https://manual.joomla.org/docs/get-started/)
- [Guia de Desenvolvimento de Módulos](https://manual.joomla.org/docs/building-extensions/modules/module-development-tutorial/)
- [Referência da API Joomla](https://api.joomla.org/)
- [Padrões de Codificação Joomla](https://manual.joomla.org/docs/get-started/codestyle/)
- [Fórum de Desenvolvedores Joomla](https://forum.joomla.org/viewforum.php?f=706)
- [GitHub do Joomla](https://github.com/joomla/joomla-cms)
- [Manual Oficial do Joomla](https://manual.joomla.org/)
