<?php

namespace Enhavo\Bundle\WorkflowBundle\Table\Widget;

use Enhavo\Bundle\AppBundle\Security\Roles\RoleUtil;
use Enhavo\Bundle\AppBundle\Table\AbstractTableWidget;

class WorkflowAssignedWidget extends AbstractTableWidget
{
    public function render($options, $item)
    {
        $templateEngine = $this->container->get('templating');
        $securityContext = $this->container->get('security.context');
        $roleUtil = new RoleUtil();
        $roleName = $roleUtil->getRoleName($item, RoleUtil::ACTION_UPDATE);
        $isGranted = false;
        if($securityContext->isGranted('WORKFLOW_UPDATE', $item)){
            $isGranted = true;
        }
        return $templateEngine->render('EnhavoWorkflowBundle:Widget:workflowAssigned.html.twig', array(
            'isGranted' => $isGranted
        ));
    }

    public function getType()
    {
        return 'workflow_assigned';
    }
}