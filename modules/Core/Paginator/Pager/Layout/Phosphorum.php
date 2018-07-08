<?php
declare(strict_types=1);

/*
 +------------------------------------------------------------------------+
 | Phalcon Platform                                                       |
 +------------------------------------------------------------------------+
 | Copyright (c) 2018 Phalcon Team                                        |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file LICENSE.txt.                             |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to license@phalconphp.com so we can send you a copy immediately.       |
 +------------------------------------------------------------------------+
*/

namespace Phosphorum\Core\Paginator\Pager\Layout;

use Phalcon\Paginator\Pager\Layout;

/**
 * Phosphorum\Core\Paginator\Pager\Layout\Phosphorum
 *
 * @package Phosphorum\Core\Paginator\Pager\Layout
 */
class Phosphorum extends Layout
{
    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $template = '<li class="page-item"><a class="page-link" href="{%url}">{%page}</a></li>';

    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $selectedTemplate = '<li class="page-item active"><span class="page-link">{%page}</span></li>';

    /**
     * {@inheritdoc}
     *
     * @param   array $options
     * @return string
     */
    public function getRendered(array $options = [])
    {
        $result = '<ul class="pagination">';

        $bootstrapSelected = '<li class="page-item disabled"><span class="page-link">{%page}</span></li>';
        $originTemplate = $this->selectedTemplate;
        $this->selectedTemplate = $bootstrapSelected;

        $this->addMaskReplacement('page', '<i class="zmdi zmdi-chevron-left"></i>', true);
        $options['page_number'] = $this->pager->getPreviousPage();
        $result .= $this->processPage($options);

        $this->selectedTemplate = $originTemplate;
        $this->removeMaskReplacement('page');
        $result .= parent::getRendered($options);

        $this->selectedTemplate = $bootstrapSelected;

        $this->addMaskReplacement('page', '<i class="zmdi zmdi-chevron-right"></i>', true);
        $options['page_number'] = $this->pager->getNextPage();
        $result .= $this->processPage($options);

        $this->selectedTemplate = $originTemplate;

        $result .= '</ul>';

        return $result;
    }
}
