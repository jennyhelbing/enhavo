<?php
/**
 * NodeInterface.php
 *
 * @since 22/05/16
 * @author gseidel
 */

namespace Enhavo\Bundle\WorkflowBundle\Model;


interface NodeInterface
{
    public function getName();

    public function setWorkflow(WorkflowInterface $workflow);

    public function getWorkflow();
}