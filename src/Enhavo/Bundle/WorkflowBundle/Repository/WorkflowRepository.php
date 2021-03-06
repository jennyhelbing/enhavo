<?php

namespace Enhavo\Bundle\WorkflowBundle\Repository;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class WorkflowRepository extends EntityRepository
{
    public function findFormNodes($id)
    {
        //get the current workflow with the given ID
        $wf = $this->find($id);

        //get nodes
        $nodes = $wf->getNodes();

        //set all nodes but the start-node to the formNodes
        foreach ($nodes as $node) {
            if($node->getStart() != true){
                $wf->addFormNode($node);
            }
        }
        return $wf;
    }

    public function hasActiveWorkflow($resource)
    {
        $resourcePath = null;
        if(is_object($resource)){
            $resourcePath = get_class($resource);
        } else {
            $resourcePath = $resource;
        }
        $workflow = null;
        $workflows = $this->findAll();
        foreach ($workflows as $wf) {
            $wfEntities = $wf->getEntity();
            if(in_array($resourcePath, $wfEntities)){
                $workflow = $wf;
                break;
            }
        }

        if($workflow != null){
            if($workflow->getActive()){
                return true;
            }
        }
        return false;
    }
}