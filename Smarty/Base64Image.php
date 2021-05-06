<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*	    email : info@thelia.net                                                      */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      This program is free software; you can redistribute it and/or modify         */
/*      it under the terms of the GNU General Public License as published by         */
/*      the Free Software Foundation; either version 3 of the License                */
/*                                                                                   */
/*      This program is distributed in the hope that it will be useful,              */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of               */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                */
/*      GNU General Public License for more details.                                 */
/*                                                                                   */
/*      You should have received a copy of the GNU General Public License            */
/*	    along with this program. If not, see <http://www.gnu.org/licenses/>.         */
/*                                                                                   */
/*************************************************************************************/

namespace Base64Image\Smarty;

use Thelia\Core\Template\Assets\AssetResolverInterface;
use Thelia\Core\Template\ParserInterface;
use TheliaSmarty\Template\AbstractSmartyPlugin;
use TheliaSmarty\Template\SmartyPluginDescriptor;

class Base64Image extends AbstractSmartyPlugin
{
    /** @var AssetResolverInterface */
    protected $assetsResolver;

    /**
     * Base64Image constructor.
     * @param AssetResolverInterface $assetsResolver
     */
    public function __construct(AssetResolverInterface $assetsResolver)
    {
        $this->assetsResolver = $assetsResolver;
    }

    public function generateBase64Image($params, \Smarty_Internal_Template $template)
    {
        static $imageCache = [];

        $file = $params['file'];

        if (empty($file)) {
            throw new \InvalidArgumentException(
                "The 'file' parameter is missing in base64 asset function"
            );
        }

        if (! isset($imageCache[$file])) {
            /** @var \TheliaSmarty\Template\SmartyParser $smartyParser */
            $smartyParser = $template->smarty;

            $path = $this->assetsResolver->resolveAssetSourcePath(ParserInterface::TEMPLATE_ASSETS_KEY, false, $file, $smartyParser);

            $filePath = $path . DS . ltrim($file, '/');

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $filePath);
            finfo_close($finfo);

            $imageCache[$file] = "data:$mime;base64," . base64_encode(file_get_contents($filePath));
        }

        return $imageCache[$file];
    }

        /**
     * @return array
     */
    public function getPluginDescriptors()
    {
        return array(
            new SmartyPluginDescriptor("function", "base64", $this, "generateBase64Image"),
        );
    }
}
