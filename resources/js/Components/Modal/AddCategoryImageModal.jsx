import useAddCategoryImage from "@/Features/Categories/useAddCategoryImage";
import { Button, Label, Modal, TextInput } from "flowbite-react";
import { useState } from "react";

export function AddCategoryImageModal({ trigger, oxyCategoryId }) {
    const [openModal, setOpenModal] = useState(false);

    const {
        imagePreview,
        isSubmitting,
        errors,
        handleFileChange,
        handleSubmit,
    } = useAddCategoryImage(setOpenModal, oxyCategoryId);

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
                <Modal.Header>Add Category Image</Modal.Header>
                <Modal.Body>
                    <form
                        onSubmit={handleSubmit}
                        className="flex w-full flex-col gap-3"
                    >
                        <div>
                            {imagePreview.image && (
                                <div className="mt-2">
                                    <img
                                        src={imagePreview.image}
                                        alt="Preview"
                                        className="w-full rounded-lg object-cover"
                                    />
                                </div>
                            )}
                        </div>
                        <div>
                            <div className="mb-2 block">
                                <Label
                                    htmlFor="image"
                                    value="Image*"
                                    color={errors.image ? "failure" : "gray"}
                                />
                            </div>
                            <TextInput
                                id="image"
                                name="image"
                                type="file"
                                onChange={handleFileChange}
                                color={errors.image ? "failure" : "gray"}
                                helperText={errors.image}
                            />
                        </div>

                        <div className="flex items-center justify-end gap-3">
                            <Button
                                onClick={() => setOpenModal(false)}
                                color="none"
                                className="border-primary/100 border hover:bg-primary/10 text-primary/100 text-nowrap"
                            >
                                Cancel
                            </Button>
                            <Button
                                type="submit"
                                disabled={isSubmitting}
                                color="none"
                                className="bg-primary hover:bg-yellow-500 text-white text-nowrap"
                            >
                                Save
                            </Button>
                        </div>
                    </form>
                </Modal.Body>
            </Modal>
        </>
    );
}
