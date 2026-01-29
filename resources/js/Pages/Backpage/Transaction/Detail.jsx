import BackpageLayout from "@/Layouts/BackpageLayout";
import { formatDateToEnglish } from "@/Utils/formatDateToEnglish";
import { formatRupiah } from "@/Utils/formatNumber";
import { Link, usePage } from "@inertiajs/react";
import { FaInfoCircle } from "react-icons/fa";

export default function DetailTransactionPage() {
    const { transaction, itemMasters, locations, customer } = usePage().props;

    return (
        <BackpageLayout>
            <div>
                <div className="grid grid-cols-1 gap-2 lg:grid-cols-2">
                    <div className="relative overflow-x-auto">
                        <table className="w-full text-left text-sm text-gray-700 rtl:text-right">
                            <tbody>
                                <tr className="bg-white">
                                    <td className="w-1/5 py-2 pr-2 font-bold">
                                        TRANSACTION
                                    </td>
                                </tr>
                                <tr className="bg-white">
                                    <td className="w-1/5 py-2 pr-2">Id</td>
                                    <td className="w-3 px-2 py-2">:</td>
                                    <td className="w-full px-2 py-2">
                                        {transaction.id}
                                    </td>
                                </tr>
                                <tr className="bg-white">
                                    <td className="w-1/5 py-2 pr-2">
                                        Created At
                                    </td>
                                    <td className="w-3 px-2 py-2">:</td>
                                    <td className="w-full px-2 py-2">
                                        {formatDateToEnglish(
                                            transaction.created_at ?? "",
                                            true,
                                        )}
                                    </td>
                                </tr>

                                <tr className="bg-white">
                                    <td className="w-1/5 py-2 pr-2">Status</td>
                                    <td className="w-3 px-2 py-2">:</td>
                                    <td className="w-full px-2 py-2">
                                        {transaction.status}
                                    </td>
                                </tr>
                                <tr className="bg-white">
                                    <td className="w-1/5 py-2 pr-2">
                                        Fulfillment Type
                                    </td>
                                    <td className="w-3 px-2 py-2">:</td>
                                    <td className="w-full px-2 py-2">
                                        {transaction.fulfillment_type}
                                    </td>
                                </tr>
                                <tr className="bg-white">
                                    <td className="w-1/5 py-2 pr-2">
                                        Subtotal
                                    </td>
                                    <td className="w-3 px-2 py-2">:</td>
                                    <td className="w-full px-2 py-2">
                                        {formatRupiah(transaction.subtotal)}
                                    </td>
                                </tr>
                                <tr className="bg-white">
                                    <td className="w-1/5 py-2 pr-2">
                                        Discount
                                    </td>
                                    <td className="w-3 px-2 py-2">:</td>
                                    <td className="w-full px-2 py-2">
                                        {formatRupiah(transaction.discount)}
                                    </td>
                                </tr>
                                <tr className="bg-white">
                                    <td className="w-1/5 py-2 pr-2">
                                        Shipping Cost
                                    </td>
                                    <td className="w-3 px-2 py-2">:</td>
                                    <td className="w-full px-2 py-2">
                                        {formatRupiah(
                                            transaction.shipping_cost,
                                        )}
                                    </td>
                                </tr>
                                <tr className="bg-white">
                                    <td className="w-1/5 py-2 pr-2">Total</td>
                                    <td className="w-3 px-2 py-2">:</td>
                                    <td className="w-full px-2 py-2">
                                        {formatRupiah(transaction.total)}
                                    </td>
                                </tr>
                                <tr className="bg-white">
                                    <td className="w-1/5 py-2 pr-2">
                                        Location Shopping
                                    </td>
                                    <td className="w-3 px-2 py-2">:</td>
                                    <td className="w-full px-2 py-2">
                                        {(() => {
                                            const location = locations.find(
                                                (loc) =>
                                                    loc.id ===
                                                    transaction.oxy_location_id,
                                            );

                                            return location ? (
                                                <Link
                                                    href={`/locations/${location.code}`}
                                                    className="text-sm text-nowrap text-left text-blue-500 underline"
                                                >
                                                    {location.name}
                                                </Link>
                                            ) : (
                                                "-"
                                            );
                                        })()}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div className="relative overflow-x-auto">
                        <span className="w-1/5 py-2 pr-2 font-bold text-sm text-gray-700 rtl:text-right text-left">
                            ITEMS
                        </span>

                        {transaction.items.map((item) => (
                            <>
                                <div className="flex gap-2 w-full">
                                    <Link
                                        href={`/item-masters/${item.oxy_item_master_id}`}
                                        className="w-1/5 py-2 pr-2 text-sm text-nowrap text-left text-blue-500 underline"
                                    >
                                        {itemMasters.find(
                                            (itemMaster) =>
                                                itemMaster.itemMasterId ===
                                                item.oxy_item_master_id,
                                        )?.name || "-"}
                                    </Link>
                                </div>
                                <table className="w-full text-left text-sm text-gray-700 rtl:text-right">
                                    <tbody>
                                        <tr className="bg-white">
                                            <td className="w-1/5 py-2 pr-2">
                                                Quantity
                                            </td>
                                            <td className="w-3 px-2 py-2">:</td>
                                            <td className="w-full px-2 py-2">
                                                {item.quantity}
                                            </td>
                                        </tr>
                                        <tr className="bg-white">
                                            <td className="w-1/5 py-2 pr-2">
                                                Price
                                            </td>
                                            <td className="w-3 px-2 py-2">:</td>
                                            <td className="w-full px-2 py-2">
                                                {formatRupiah(item.price)}
                                            </td>
                                        </tr>
                                        <tr className="bg-white">
                                            <td className="w-1/5 py-2 pr-2">
                                                Total
                                            </td>
                                            <td className="w-3 px-2 py-2">:</td>
                                            <td className="w-full px-2 py-2">
                                                {formatRupiah(item.subtotal)}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </>
                        ))}
                    </div>
                    <div className="relative overflow-x-auto">
                        <span className="w-1/5 py-2 pr-2 font-bold text-sm text-gray-700 rtl:text-right text-left">
                            CUSTOMER
                        </span>

                        <table className="w-full text-left text-sm text-gray-700 rtl:text-right">
                            <tbody>
                                <tr className="bg-white">
                                    <td className="w-1/5 py-2 pr-2">Id</td>
                                    <td className="w-3 px-2 py-2">:</td>
                                    <td className="w-full px-2 py-2">
                                        {customer.id || "-"}
                                    </td>
                                </tr>

                                <tr className="bg-white">
                                    <td className="w-1/5 py-2 pr-2">Name</td>
                                    <td className="w-3 px-2 py-2">:</td>
                                    <td className="w-full px-2 py-2">
                                        {customer.name || "-"}
                                    </td>
                                </tr>

                                <tr className="bg-white">
                                    <td className="w-1/5 py-2 pr-2">Address</td>
                                    <td className="w-3 px-2 py-2">:</td>
                                    <td className="w-full px-2 py-2">
                                        {customer.address || "-"}
                                    </td>
                                </tr>

                                <tr className="bg-white">
                                    <td className="w-1/5 py-2 pr-2">City</td>
                                    <td className="w-3 px-2 py-2">:</td>
                                    <td className="w-full px-2 py-2">
                                        {customer.city || "-"}
                                    </td>
                                </tr>

                                <tr className="bg-white">
                                    <td className="w-1/5 py-2 pr-2">Code</td>
                                    <td className="w-3 px-2 py-2">:</td>
                                    <td className="w-full px-2 py-2">
                                        {customer.code || "-"}
                                    </td>
                                </tr>

                                <tr className="bg-white">
                                    <td className="w-1/5 py-2 pr-2">Phone</td>
                                    <td className="w-3 px-2 py-2">:</td>
                                    <td className="w-full px-2 py-2">
                                        {customer.phone || customer.hp || "-"}
                                    </td>
                                </tr>

                                <tr className="bg-white">
                                    <td className="w-1/5 py-2 pr-2">Email</td>
                                    <td className="w-3 px-2 py-2">:</td>
                                    <td className="w-full px-2 py-2">
                                        {customer.email || "-"}
                                    </td>
                                </tr>

                                <tr className="bg-white">
                                    <td className="w-1/5 py-2 pr-2">
                                        Date of Birth
                                    </td>
                                    <td className="w-3 px-2 py-2">:</td>
                                    <td className="w-full px-2 py-2">
                                        {customer.dob || "-"}
                                    </td>
                                </tr>

                                <tr className="bg-white">
                                    <td className="w-1/5 py-2 pr-2">Gender</td>
                                    <td className="w-3 px-2 py-2">:</td>
                                    <td className="w-full px-2 py-2">
                                        {customer.gender || "-"}
                                    </td>
                                </tr>

                                <tr className="bg-white">
                                    <td className="w-1/5 py-2 pr-2">NPWP</td>
                                    <td className="w-3 px-2 py-2">:</td>
                                    <td className="w-full px-2 py-2">
                                        {customer.npwp || "-"}
                                    </td>
                                </tr>

                                <tr className="bg-white">
                                    <td className="w-1/5 py-2 pr-2">Note</td>
                                    <td className="w-3 px-2 py-2">:</td>
                                    <td className="w-full px-2 py-2">
                                        {customer.note || "-"}
                                    </td>
                                </tr>

                                <tr className="bg-white">
                                    <td className="w-1/5 py-2 pr-2">
                                        Username
                                    </td>
                                    <td className="w-3 px-2 py-2">:</td>
                                    <td className="w-full px-2 py-2">
                                        {customer.username || "-"}
                                    </td>
                                </tr>

                                <tr className="bg-white">
                                    <td className="w-1/5 py-2 pr-2">
                                        Location Register
                                    </td>
                                    <td className="w-3 px-2 py-2">:</td>
                                    <td className="w-full px-2 py-2">
                                        {(() => {
                                            const location = locations.find(
                                                (loc) =>
                                                    loc.id ===
                                                    customer.locationRegisterId,
                                            );

                                            return location ? (
                                                <Link
                                                    href={`/locations/${location.code}`}
                                                    className="text-sm text-nowrap text-left text-blue-500 underline"
                                                >
                                                    {location.name}
                                                </Link>
                                            ) : (
                                                "-"
                                            );
                                        })()}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    {transaction.fulfillment_type === "SHIPMENT" && (
                        <div className="relative overflow-x-auto">
                            <span className="w-1/5 py-2 pr-2 font-bold text-sm text-gray-700 rtl:text-right text-left">
                                SHIPMENT
                            </span>

                            <table className="w-full text-left text-sm text-gray-700 rtl:text-right">
                                <tbody>
                                    <tr className="bg-white">
                                        <td className="w-1/5 py-2 pr-2">
                                            Grab Delivery ID
                                        </td>
                                        <td className="w-3 px-2 py-2">:</td>
                                        <td className="w-full px-2 py-2">
                                            {transaction.shipment
                                                ?.grab_delivery_id || "-"}
                                        </td>
                                    </tr>

                                    <tr className="bg-white">
                                        <td className="w-1/5 py-2 pr-2">
                                            Shipping Cost
                                        </td>
                                        <td className="w-3 px-2 py-2">:</td>
                                        <td className="w-full px-2 py-2">
                                            {transaction.shipment
                                                ?.grab_shipping_cost
                                                ? formatRupiah(
                                                      transaction.shipment
                                                          .grab_shipping_cost,
                                                  )
                                                : "-"}
                                        </td>
                                    </tr>

                                    <tr className="bg-white">
                                        <td className="w-1/5 py-2 pr-2">
                                            Status
                                        </td>
                                        <td className="w-3 px-2 py-2">:</td>
                                        <td className="w-full px-2 py-2">
                                            {transaction.shipment?.status ||
                                                "-"}
                                        </td>
                                    </tr>

                                    <tr className="bg-white">
                                        <td className="w-1/5 py-2 pr-2">
                                            Receiver Name
                                        </td>
                                        <td className="w-3 px-2 py-2">:</td>
                                        <td className="w-full px-2 py-2">
                                            {transaction.shipment
                                                ?.receiver_name || "-"}
                                        </td>
                                    </tr>

                                    <tr className="bg-white">
                                        <td className="w-1/5 py-2 pr-2">
                                            Receiver Phone
                                        </td>
                                        <td className="w-3 px-2 py-2">:</td>
                                        <td className="w-full px-2 py-2">
                                            {transaction.shipment
                                                ?.receiver_phone_number || "-"}
                                        </td>
                                    </tr>

                                    <tr className="bg-white">
                                        <td className="w-1/5 py-2 pr-2">
                                            Receiver Address
                                        </td>
                                        <td className="w-3 px-2 py-2">:</td>
                                        <td className="w-full px-2 py-2">
                                            {transaction.shipment
                                                ?.receiver_address || "-"}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    )}
                    {transaction.fulfillment_type === "PICKUP" && (
                        <div className="relative overflow-x-auto">
                            <span className="w-1/5 py-2 pr-2 font-bold text-sm text-gray-700 rtl:text-right text-left">
                                PICKUP
                            </span>

                            <table className="w-full text-left text-sm text-gray-700 rtl:text-right">
                                <tbody>
                                    <tr className="bg-white">
                                        <td className="w-1/5 py-2 pr-2">
                                            Pickup Code
                                        </td>
                                        <td className="w-3 px-2 py-2">:</td>
                                        <td className="w-full px-2 py-2">
                                            {transaction.pickup?.pickup_code ||
                                                transaction.pickup?.code ||
                                                "-"}
                                        </td>
                                    </tr>

                                    <tr className="bg-white">
                                        <td className="w-1/5 py-2 pr-2">
                                            Pickup Time
                                        </td>
                                        <td className="w-3 px-2 py-2">:</td>
                                        <td className="w-full px-2 py-2">
                                            {transaction.pickup?.pickup_time
                                                ? formatDateToEnglish(
                                                      transaction.pickup
                                                          .pickup_time,
                                                      true,
                                                  )
                                                : "-"}
                                        </td>
                                    </tr>

                                    <tr className="bg-white">
                                        <td className="w-1/5 py-2 pr-2">
                                            Pickup End Time
                                        </td>
                                        <td className="w-3 px-2 py-2">:</td>
                                        <td className="w-full px-2 py-2">
                                            {transaction.pickup?.pickup_end_time
                                                ? formatDateToEnglish(
                                                      transaction.pickup
                                                          .pickup_end_time,
                                                      true,
                                                  )
                                                : "-"}
                                        </td>
                                    </tr>

                                    <tr className="bg-white">
                                        <td className="w-1/5 py-2 pr-2">
                                            Receiver Name
                                        </td>
                                        <td className="w-3 px-2 py-2">:</td>
                                        <td className="w-full px-2 py-2">
                                            {transaction.pickup
                                                ?.receiver_name || "-"}
                                        </td>
                                    </tr>

                                    <tr className="bg-white">
                                        <td className="w-1/5 py-2 pr-2">
                                            Receiver Phone
                                        </td>
                                        <td className="w-3 px-2 py-2">:</td>
                                        <td className="w-full px-2 py-2">
                                            {transaction.pickup
                                                ?.receiver_phone_number || "-"}
                                        </td>
                                    </tr>

                                    <tr className="bg-white">
                                        <td className="w-1/5 py-2 pr-2">
                                            Status
                                        </td>
                                        <td className="w-3 px-2 py-2">:</td>
                                        <td className="w-full px-2 py-2">
                                            {transaction.pickup?.status || "-"}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    )}
                </div>
            </div>
        </BackpageLayout>
    );
}
