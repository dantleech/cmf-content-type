<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2016 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Component\ContentType\Storage\Doctrine\PhpcrOdm;

use PHPCR\SessionInterface;

/**
 * Encapsulates the logic for registering system node types.
 */
final class NodeTypeRegistrator
{
    private $encoder;

    public function __construct(PropertyEncoder $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * Register the content-type node types with the given PHPCR session.
     *
     * @param SessionInterface
     */
    public function registerNodeTypes(SessionInterface $session)
    {
        $cnd = sprintf(
            '<%s=\'https://github.com/symfony-cmf/content-type\'>',
            $this->encoder->getPrefix(),
            $this->encoder->getUri()
        );

        $nodeTypeManager = $session->getWorkspace()->getNodeTypeManager();
        $nodeTypeManager->registerNodeTypesCnd($cnd, true);
    }
}
