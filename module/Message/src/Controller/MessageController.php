<?php

namespace Message\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Message\Service\MessageService;
use Message\Form\MessageForm;
use Message\Model\Message;
use User\Service\AuthService;
use Deal\Service\DealService;
use User\Service\UserService;

class MessageController extends AbstractActionController
{
    protected $messageService;
    protected $authService;
    protected $dealService;
    protected $userService;

    public function __construct(
        MessageService $messageService,
        AuthService $authService,
        DealService $dealService,
        UserService $userService
    ) {
        $this->messageService = $messageService;
        $this->authService = $authService;
        $this->dealService = $dealService;
        $this->userService = $userService;
    }

    public function indexAction(): ViewModel
    {
        if (!$this->authService->hasIdentity()) {
            return $this->redirect()->toRoute('user/login');
        }

        $userId = $this->authService->getIdentity()->getId();
        $type = $this->params()->fromQuery('type', 'received');
        
        $messages = $this->messageService->findByUserId($userId, $type);
        $unreadCount = $this->messageService->getUnreadCount($userId);

        return new ViewModel([
            'messages' => $messages,
            'type' => $type,
            'unreadCount' => $unreadCount,
        ]);
    }

    public function viewAction(): ViewModel
    {
        if (!$this->authService->hasIdentity()) {
            return $this->redirect()->toRoute('user/login');
        }

        $id = (int) $this->params()->fromRoute('id');
        $message = $this->messageService->findById($id);
        $userId = $this->authService->getIdentity()->getId();

        if (!$message || ($message->getToUserId() !== $userId && $message->getFromUserId() !== $userId)) {
            return $this->notFoundAction();
        }

        if ($message->getToUserId() === $userId && !$message->getIsRead()) {
            $this->messageService->markAsRead($id, $userId);
        }

        return new ViewModel([
            'message' => $message,
        ]);
    }

    public function createAction(): ViewModel
    {
        if (!$this->authService->hasIdentity()) {
            return $this->redirect()->toRoute('user/login');
        }

        $form = new MessageForm();
        $dealId = (int) $this->params()->fromQuery('deal_id');
        $toUserId = (int) $this->params()->fromQuery('to_user_id');

        if ($dealId) {
            $deal = $this->dealService->findById($dealId);
            if ($deal && $deal->getUserId() !== $this->authService->getIdentity()->getId()) {
                $form->get('deal_id')->setValue($dealId);
                $form->get('to_user_id')->setValue($deal->getUserId());
                $form->get('subject')->setValue('Re: ' . $deal->getTitle());
            }
        }

        if ($toUserId) {
            $form->get('to_user_id')->setValue($toUserId);
        }

        $request = $this->getRequest();

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $message = new Message();
                $message->setFromUserId($this->authService->getIdentity()->getId())
                    ->setToUserId((int) $data['to_user_id'])
                    ->setDealId($data['deal_id'] ? (int) $data['deal_id'] : null)
                    ->setSubject($data['subject'])
                    ->setContent($data['content'])
                    ->setStatus('unread');

                $this->messageService->create($message);
                $this->flashMessenger()->addSuccessMessage('Message envoyé avec succès.');
                return $this->redirect()->toRoute('message');
            }
        }

        return new ViewModel([
            'form' => $form,
        ]);
    }

    public function replyAction(): ViewModel
    {
        if (!$this->authService->hasIdentity()) {
            return $this->redirect()->toRoute('user/login');
        }

        $id = (int) $this->params()->fromRoute('id');
        $originalMessage = $this->messageService->findById($id);
        $userId = $this->authService->getIdentity()->getId();

        if (!$originalMessage || $originalMessage->getToUserId() !== $userId) {
            return $this->notFoundAction();
        }

        $form = new MessageForm();
        $form->get('to_user_id')->setValue($originalMessage->getFromUserId());
        $form->get('deal_id')->setValue($originalMessage->getDealId());
        $form->get('subject')->setValue('Re: ' . $originalMessage->getSubject());

        $request = $this->getRequest();

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $message = new Message();
                $message->setFromUserId($userId)
                    ->setToUserId((int) $data['to_user_id'])
                    ->setDealId($data['deal_id'] ? (int) $data['deal_id'] : null)
                    ->setSubject($data['subject'])
                    ->setContent($data['content'])
                    ->setStatus('unread');

                $this->messageService->create($message);
                $this->flashMessenger()->addSuccessMessage('Réponse envoyée avec succès.');
                return $this->redirect()->toRoute('message');
            }
        }

        return new ViewModel([
            'form' => $form,
            'originalMessage' => $originalMessage,
        ]);
    }
}

