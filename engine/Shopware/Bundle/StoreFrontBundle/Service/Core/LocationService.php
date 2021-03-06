<?php

namespace Shopware\Bundle\StoreFrontBundle\Service\Core;

use Doctrine\DBAL\Connection;
use Shopware\Bundle\StoreFrontBundle\Gateway\CountryGatewayInterface;
use Shopware\Bundle\StoreFrontBundle\Service\LocationServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Struct\Country;
use Shopware\Bundle\StoreFrontBundle\Struct\ShopContextInterface;

class LocationService implements LocationServiceInterface
{
    /**
     * @var CountryGatewayInterface
     */
    private $gateway;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * LocationService constructor.
     * @param CountryGatewayInterface $gateway
     * @param Connection $connection
     */
    public function __construct(CountryGatewayInterface $gateway, Connection $connection)
    {
        $this->gateway = $gateway;
        $this->connection = $connection;
    }

    /**
     * Returns all available countries for the provided shop context
     *
     * @param ShopContextInterface $context
     * @return Country[] indexed by id
     */
    public function getCountries(ShopContextInterface $context)
    {
        $ids = $this->getCountryIds();
        $countries = $this->gateway->getCountries($ids, $context);

        $states = $this->gateway->getCountryStates($ids, $context);

        $result = [];
        foreach ($countries as $country) {
            if (!$country->isActive()) {
                continue;
            }

            if (isset($states[$country->getId()])) {
                $country->setStates(
                    $this->sortStates($states[$country->getId()])
                );
            }

            $result[$country->getId()] = $country;
        }

        return $this->sortCountries($result);
    }

    /**
     * @return int[]
     */
    private function getCountryIds()
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('id');
        $query->from('s_core_countries', 'country');
        return $query->execute()->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * @param Country\State[] $countryStates
     * @return Country\State[]
     */
    private function sortStates($countryStates)
    {
        usort($countryStates, function (Country\State $a, Country\State $b) {
            if ($a->getPosition() == $b->getPosition()) {
                return strnatcasecmp($a->getName(), $b->getName());
            }
            return ($a->getPosition() < $b->getPosition()) ? -1 : 1;
        });

        return $countryStates;
    }

    /**
     * @param Country[] $countries
     * @return Country[]
     */
    private function sortCountries($countries)
    {
        usort($countries, function (Country $a, Country $b) {
            if ($a->getPosition() == $b->getPosition()) {
                return strnatcasecmp($a->getName(), $b->getName());
            }

            return ($a->getPosition() < $b->getPosition()) ? -1 : 1;
        });

        return $countries;
    }
}
