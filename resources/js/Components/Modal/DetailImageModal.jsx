import { formatDateToEnglish } from "@/Utils/formatDateToEnglish";
import { Modal } from "flowbite-react";
import { useState } from "react";

export function DetailImageModal({ trigger, image }) {
    const [openModal, setOpenModal] = useState(false);

    return (
        <>
            <div className="cursor-pointer" onClick={() => setOpenModal(true)}>
                {trigger}
            </div>
            <Modal
                show={openModal}
                onClose={() => setOpenModal(false)}
                size="lg"
            >
                <Modal.Header>Detail Image</Modal.Header>
                <Modal.Body>
                    <div className="space-y-4">
                        <img
                            src={image.image}
                            alt="Detail"
                            className="w-full max-h-96 object-contain rounded"
                        />

                        <div className="text-sm text-gray-600">
                            <p>
                                <span className="font-medium">Created At:</span>{" "}
                                {formatDateToEnglish(image.created_at ?? "")}
                            </p>
                            <p>
                                <span className="font-medium">Updated At:</span>{" "}
                                {formatDateToEnglish(image.updated_at ?? "")}
                            </p>
                        </div>
                    </div>
                </Modal.Body>
            </Modal>
        </>
    );
}
