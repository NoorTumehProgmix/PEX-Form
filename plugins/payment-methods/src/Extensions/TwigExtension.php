<?php
/**
 * JUZAWEB CMS - The Best CMS for Laravel Project
 *
 * @package    juzaweb/cms
 * @author     The Anh Dang <dangtheanh16@gmail.com>
 * @link       https://juzaweb.com/cms
 * @license    MIT
 */

namespace Progmix\PaymentMethods\Extensions;

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

class TwigExtension extends AbstractExtension
{
    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'App_Payment_Methods_Custom';
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_payment_methods', 'get_payment_methods'),
        ];
    }
}
