# WooCommerce Russian Post Letter Print
Быстрый способ напечатать конверт С6 (с заполненными данными из заказа) для отправки через Почту России

![Demo](https://github.com/troyanov/woocommerce-rp-letter-print/raw/master/demo.gif)

# Описание
Данный плагин генерирует PDF документ с заполненной формой конверта, которую можно использовать для печати.

# Установка
У вас должен быть установлен [mPDF](https://mpdf.github.io/) версии [6.1](https://github.com/mpdf/mpdf/tree/6.1)
В случае если он у вас не установлен, вы можете скачать его и поместить в каталог `custom/mpdf` данного плагина.

Если же mPDF размещен где-то в другом месте, то необходимо исправить путь в файле `woocommerce-rp-letter-print.php`
```php
require_once dirname(__FILE__).'/custom/mpdf/vendor/autoload.php';
```
Для корректной печати индекса получателя, необходимо добавить шрифт [ZIPcode.ttf](https://github.com/troyanov/woocommerce-rp-letter-print/raw/master/custom/assets/fonts/ZIPcode.ttf) в каталог `mpdf/ttfonts`.
После добавления, так же необходимо прописать информацию о шрифте в настройках `mpdf/config_fonts`

```php
$this->fontdata = array(
    "zipcode" => array(
        'R' => "ZIPcode.ttf",
        ),
    // прочие шрифты
    );
```
    
Следуйте общим инструкциям установки плагинов для WordPress. После активации необходимо настроить плагин, для заполениня полей "От кого" и "Откуда" (вкладка **Печать Конвертов** в меню настроек WooCommerce).

# Совместимость
* WordPress = 4.7
* WooCommerce = 2.6.11
* Storefront* = 2.1.6

**Плагин разрабатывался под конкретную задачу, для бесплатной темы WooCommerce Storefront и не тестировался с другими темами.*
