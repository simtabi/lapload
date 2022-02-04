![lapload](https://banners.beyondco.de/Livewire%20Image%20Uploader.png?theme=light&packageManager=composer+require&packageName=composer+require+simtabi%2Flapload&pattern=endlessClouds&style=style_1&description=&md=0&showWatermark=0&fontSize=100px&images=photograph&widths=400&heights=400)

## Demo
![uploader-demo](https://media.giphy.com/media/vcUG1LGyCEZV8HkKmG/giphy.gif)

## Features
- Upload image
- Upload multiple images
- Remove image

## Requirements
- Laravel Livewire 2.0

## Installation
You can install the package via composer:
```bash
composer require simtabi/lapload
```
## Usage
Add the Livewire directive into component blade page (E.g form-component.blade.php).
```html
<livewire:lapload" name="foo"> 
or
<livewire:lapload" name="foo" size="2048" multiple>
```
| Props      | Type     | Required | Description|
| -----------| ---------| :------: |-----------|
| name       | string   |   ✅    |Parent component public properties to store uploaded images name.|
| multiple   | bool     |   ❌    |Enable multiple upload. (Default: false)|
| size       | int      |   ❌    | Image size limit. (Default: 1024KB)|

Add public properties to store the name of the images uploaded in array and use the ImageUploader trait in the component code (E.g FormComponent.php).

```PHP
<?php
namespace App\Http\Livewire;
.
.
use Simtabi\Lapload\Traits\HasLapload;

class FormComponent extends Component{
    use HasLapload;
    public $foo;
}
```
Every time there are changes on the images (remove/upload), the component will trigger an event which trigger method in the parent component. The method will update the $foo properties with an array of new images name.

## Uploaded File
For now all uploaded images will be stored inside storage/public/lapload. To access the image from the frontend you need to create a symbolic link from public/storage to storage/app/public. To create the symbolic link run:
```bash
php artisan storage:link
```
To display the image in frontend:
```html
<img src="{{ asset('storage/lapload/' . $imageName) }}">
```

## Styling
To add the styling, you need publish the package assets folder to your project public folder. To publish the package assets folder run:
```bash
php artisan vendor:publish --provider="Simtabi\Lapload\LaploadServiceProvider" --tag="assets"
```
Next, include the css file inside the assets folder in your HTML page <head> section:
```html
<head>
     ...
    <link href="{{ asset('lapload/css/app.css') }}" rel="stylesheet">
</head>
```

