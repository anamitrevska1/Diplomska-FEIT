<?php

namespace App\Helpers;

use chillerlan\QRCode\Output\QRCodeOutputException;
use chillerlan\QRCode\Output\QRGdImagePNG;

class QRImageWithLogo extends QRGdImagePNG
{

    /**
     * @throws \chillerlan\QRCode\Output\QRCodeOutputException
     */
    public function dump(string|null $file = null, string|null $logo = null):string{
        $logo ??= '';

        // set returnResource to true to skip further processing for now
        $this->options->returnResource = true;

        // of course, you could accept other formats too (such as resource or Imagick)
        // I'm not checking for the file type either for simplicity reasons (assuming PNG)
        if(!is_file($logo) || !is_readable($logo)){
            throw new QRCodeOutputException('invalid logo');
        }

        // there's no need to save the result of dump() into $this->image here
        parent::dump($file);

        $im = imagecreatefrompng($logo);

        if($im === false){
            throw new QRCodeOutputException('imagecreatefrompng() error');
        }

        // get logo image size
        $w = imagesx($im);
        $h = imagesy($im);

        // set new logo size, leave a border of 1 module (no proportional resize/centering)
        $lw = (($this->options->logoSpaceWidth - 2) * $this->options->scale);
        $lh = (($this->options->logoSpaceHeight - 2) * $this->options->scale);

        // get the qrcode size
        $ql = ($this->matrix->getSize() * $this->options->scale);

        // scale the logo and copy it over. done!
        imagecopyresampled($this->image, $im, (($ql - $lw) / 2), (($ql - $lh) / 2), 0, 0, $lw, $lh, $w, $h);

        $imageData = $this->dumpImage();

        $this->saveToFile($imageData, $file);

        if($this->options->outputBase64){
            $imageData = $this->toBase64DataURI($imageData);
        }

        return $imageData;
    }

}
