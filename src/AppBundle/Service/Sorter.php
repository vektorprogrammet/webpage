<?php

namespace AppBundle\Service;

use AppBundle\Entity\GroupMemberInterface;
use AppBundle\Entity\User;
use AppBundle\Entity\Receipt;

class Sorter
{
    /**
     * @param User $user1
     * @param User $user2
     *
     * @return int
     */
    protected function userWithNewestReceipt($user1, $user2)
    {
        $user1Receipts = $user1->getReceipts()->toArray();
        $user2Receipts = $user2->getReceipts()->toArray();

        $this->sortReceiptsBySubmitTime($user1Receipts);
        $this->sortReceiptsBySubmitTime($user2Receipts);

        if (empty($user1Receipts) && empty($user2Receipts)) {
            return 0;
        }

        if (empty($user1Receipts)) {
            return 1;
        }

        if (empty($user2Receipts)) {
            return -1;
        }

        return $this->newestReceipt($user1Receipts[0], $user2Receipts[0]);
    }

    /**
     * @param Receipt $receipt1
     * @param Receipt $receipt2
     *
     * @return int
     */
    public function newestReceipt($receipt1, $receipt2)
    {
        if ($receipt1->getSubmitDate() === $receipt2->getSubmitDate()) {
            return 0;
        }

        return ($receipt1->getSubmitDate() > $receipt2->getSubmitDate()) ? -1 : 1;
    }

    /**
     * @param User[] $users
     *
     * @return bool success
     */
    public function sortUsersByReceiptSubmitTime(&$users)
    {
        return usort($users, array($this, 'userWithNewestReceipt'));
    }

    /**
     * @param User[] $users
     */
    public function sortUsersByReceiptStatus(&$users)
    {
        $usersWithPendingReceipts = [];
        $usersWithoutPendingReceipts = [];
        foreach ($users as $user) {
            if ($user->hasPendingReceipts()) {
                array_push($usersWithPendingReceipts, $user);
            } else {
                array_push($usersWithoutPendingReceipts, $user);
            }
        }

        $users = array_merge($usersWithPendingReceipts, $usersWithoutPendingReceipts);
    }

    /**
     * @param Receipt[] $receipts
     *
     * @return bool success
     */
    public function sortReceiptsBySubmitTime(&$receipts)
    {
        return usort($receipts, array($this,'newestReceipt'));
    }

    /**
     * @param Receipt[] $receipts
     */
    public function sortReceiptsByStatus(&$receipts)
    {
        $pendingReceipts = [];
        $nonPendingReceipts = [];
        foreach ($receipts as $receipt) {
            if ($receipt->getStatus() === Receipt::STATUS_PENDING) {
                array_push($pendingReceipts, $receipt);
            } else {
                array_push($nonPendingReceipts, $receipt);
            }
        }
        $receiptElements = array_merge($pendingReceipts, $nonPendingReceipts);
        $receipts = $receiptElements;
    }

    /**
     * Sorts "leder" og "nestleder" first and the rest in alphabetical order
     *
     * @param User[] $users
     */
    public function sortUsersByActiveExecutiveBoardPosition(&$users)
    {
        $this->sortUsersByActivePositions($users, 'getActiveExecutiveBoardMemberships');
    }

    /**
     * Sorts "leder" og "nestleder" first and the rest in alphabetical order
     *
     * @param User[] $users
     */
    public function sortUsersByActiveTeamPosition(&$users)
    {
        $this->sortUsersByActivePositions($users, 'getActiveTeamMemberships');
    }

    /**
     * @param User[] $users
     * @param string $getActiveTeamMembershipsFunction
     */
    private function sortUsersByActivePositions(&$users, $getActiveTeamMembershipsFunction)
    {
        usort($users, function ($user1, $user2) use ($getActiveTeamMembershipsFunction) {
            // Get workhistories
            $teamMemberships1 = call_user_func(array($user1, $getActiveTeamMembershipsFunction));
            $teamMemberships2 = call_user_func(array($user2, $getActiveTeamMembershipsFunction));

            // Check if empty or null
            if ($teamMemberships2 === null || empty($teamMemberships2)) {
                if ($teamMemberships1 === null || empty($teamMemberships1)) {
                    return 0; // Both null or empty
                }
                return -1; // If 2 is empty, but not 1:TeamMember 1 comes first
            } elseif ($teamMemberships1 === null || empty($teamMemberships1)) {
                return 1; // If 1 is empty, but not 2: 2 comes first
            }

            // Sort workhistories by position
            $this->sortTeamMembershipsByPosition($teamMemberships1);
            $this->sortTeamMembershipsByPosition($teamMemberships2);

            $cmp = 0;
            for ($i = 0; $i < min(count($teamMemberships1), count($teamMemberships2)); $i++) {
                $cmp = $this->compareTeamMemberships($teamMemberships1[$i], $teamMemberships2[$i]);
                if ($cmp !== 0) {
                    return $cmp; // Non equal positions
                }
            }
            if (count($teamMemberships1) === count($teamMemberships2)) {
                return count($teamMemberships2) - count($teamMemberships1);
            } else {
                return $cmp;
            }
        });
    }

    /**
     * Order: "leder" < "nestleder" < "aaa" < "zzz"
     *
     * @param GroupMemberInterface[] $teamMemberships
     *
     * @return GroupMemberInterface[]
     */
    public function sortTeamMembershipsByPosition(&$teamMemberships)
    {
        usort($teamMemberships, array($this, 'compareTeamMemberships'));
    }

    /**
     * @param GroupMemberInterface $teamMembership1
     * @param GroupMemberInterface $teamMembership2
     *
     * @return int -1, 0, 1
     */
    private function compareTeamMemberships($teamMembership1, $teamMembership2)
    {
        return $this->comparePositions($teamMembership1->getPositionName(), $teamMembership2->getPositionName());
    }

    /**
     * Order: "leder" < "nestleder" < "aaa" < "zzz"
     *
     * @param string $position1
     * @param string $position2
     *
     * @return int -1, 0, 1
     */
    private function comparePositions($position1, $position2)
    {
        // Normalize
        $position1 = strtolower($position1);
        $position2 = strtolower($position2);

        if ($position1 === $position2) {
            return 0;
        }

        if ($position1 === 'leder') {
            return -1;
        }
        if ($position2 === 'leder') {
            return 1;
        }
        if ($position1 === 'nestleder') {
            return -1;
        }
        if ($position2 === 'nestleder') {
            return 1;
        }

        return strcmp($position1, $position2);
    }
}
