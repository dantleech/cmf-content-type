<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2016 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Component\ContentType\OptionsResolver;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Option resolver that allows options to be designated as either for the
 * content-type form, or the content-type view.
 *
 * All options that are not assigned will be to both the view and the form.
 */
class FieldOptionsResolver extends OptionsResolver
{
    private $formOptions = [];
    private $viewOptions = [];

    /**
     * Set which options should be passed only to the form.
     *
     * @param array $formOptions
     */
    public function setFormOptions(array $formOptions)
    {
        $this->formOptions = $formOptions;
    }

    /**
     * Set which options should be passed only to the view.
     *
     * @param array $viewOptions
     */
    public function setViewOptions(array $viewOptions)
    {
        $this->viewOptions = $viewOptions;
    }

    /**
     * Resolve the form options.
     *
     * @param array $options
     */
    public function resolveFormOptions(array $options = [])
    {
        $options = $this->resolve($options);

        foreach ($this->viewOptions as $optionName) {
            unset($options[$optionName]);
        }

        return $options;
    }

    /**
     * Resolve the view options.
     *
     * @param array $options
     */
    public function resolveViewOptions(array $options = [])
    {
        $options = $this->resolve($options);

        foreach ($this->formOptions as $optionName) {
            unset($options[$optionName]);
        }

        return $options;
    }
}
