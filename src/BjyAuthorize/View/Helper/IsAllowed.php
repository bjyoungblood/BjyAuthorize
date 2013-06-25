<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\View\Helper;

use Zend\View\Helper\AbstractHelper;
use BjyAuthorize\Service\Authorize;

/**
 * IsAllowed View helper. Allows checking access to a resource/privilege in views.
 *
 * @author Ben Youngblood <bx.youngblood@gmail.com>
 */
class IsAllowed extends AbstractHelper
{
    /**
     * @var Authorize
     */
    protected $authorizeService;

    /**
     * @param Authorize $authorizeService
     */
    public function __construct(Authorize $authorizeService)
    {
        $this->authorizeService = $authorizeService;
    }

    /**
     * @param mixed      $resource
     * @param mixed|null $privilege
     *
     * @return bool
     */
    public function __invoke($resource, $privilege = null)
    {
        return $this->authorizeService->isAllowed($resource, $privilege);
    }
}
