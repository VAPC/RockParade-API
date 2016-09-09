<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Infrasctucture\AbstractRepository;
use AppBundle\Entity\Link;

/** {@inheritDoc} */
class LinkRepository extends AbstractRepository
{

    /**
     * @return Link|object|null
     */
    public function findOneByUrl(string $linkUrl)
    {
        return $this->findOneBy(
            [
                'url' => $linkUrl,
            ]
        );
    }

    public function getOrCreateLink(string $url, string $description = null): Link
    {
        $link = $this->findOneByUrl($url);

        if (!$link) {
            $link = new Link($url, $description);
        }

        return $link;
    }
}
