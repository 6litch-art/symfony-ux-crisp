<?php

namespace Symfony\UX\Crisp\Subscriber;

use \Symfony\Component\HttpKernel\Event\RequestEvent;
use \Symfony\Component\HttpFoundation\Response;

use Twig\Environment;
use Base\Service\BaseService;
use EasyCorp\Bundle\EasyAdminBundle\Provider\AdminContextProvider;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\KernelInterface;

class CrispListener
{
    private $twig;

    public function __construct(ParameterBagInterface $parameterBag, Environment $twig, RequestStack $requestStack)
    {
        $this->twig         = $twig;
        $this->requestStack = $requestStack;

        $this->enable = $parameterBag->get("crisp.enable");
        $this->autoAppend = $parameterBag->get("crisp.autoappend");
        $this->websiteId = $parameterBag->get("crisp.website_id");
    }

    private function allowRender(ResponseEvent $event)
    {
        if (!$this->websiteId) return;

        if (!$this->autoAppend)
            return false;
        
            $contentType = $event->getResponse()->headers->get('content-type');
        if ($contentType && !str_contains($contentType, "text/html"))
            return false;
    
        if (!$event->isMainRequest())
            return false;
        
        return true;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        if (!$this->enable) return;
        if (!$this->websiteId) return;

        $locale = substr($event->getRequest()->getLocale(),0,2);
        $javascript = '<script type="text/javascript">'.
                           'window.$crisp = [];'.
                           'window.CRISP_RUNTIME_CONFIG = {locale : "'.$locale.'"};'.
                           'window.CRISP_WEBSITE_ID = "'.$this->websiteId.'";'.
                           '(function () {'.
                               'd = document;'.
                               's = d.createElement("script");'.
                               's.src = "https://client.crisp.chat/l.js";'.
                               's.async = 1;'.
                               'd.getElementsByTagName("head")[0].appendChild(s);'.
                           '})();'.
                       '</script>';

        dump($javascript);
        $this->twig->addGlobal("crisp", $this->twig->getGlobals()["crisp"] ?? "" . $javascript);
    }

    public function onKernelResponse(ResponseEvent $event)
    {
        if (!$this->allowRender($event)) return false;

        $response = $event->getResponse();
        $javascript = $this->twig->getGlobals()["crisp"] ?? "";

        $content = preg_replace(['/<\/body\b[^>]*>/'], [$javascript."$0"], $response->getContent(), 1);
        $response->setContent($content);

        return true;
    }
}