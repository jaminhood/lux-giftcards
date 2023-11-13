import axios from "axios"
import { useEffect, useState } from "react"
import Modal from "../../../../work-service-plugin/src/admin/shared/Modal"
import { pastDate } from "../../helpers"
import { API_URL } from "../../utils"

const EditHomeTable = ({ toggleOpen, editGiftcard }) => {
	const { asset_img, asset_name, customer_name, id, order_status, proof, time_stamp } = editGiftcard
	const [confirm, setConfirm] = useState(`pending`)
	const [confirmId, setConfirmId] = useState(1)

	const changeConfirm = val => {
		setConfirm(val)
	}

	useEffect(() => {
		switch (confirm) {
			case `declined`:
				setConfirmId(0)
				break
			case `pending`:
				setConfirmId(1)
				break
			case `approved`:
				setConfirmId(2)
				break
		}
	}, [confirm])

	useEffect(() => {
		switch (order_status) {
			case `0`:
				setConfirm(`declined`)
				break
			case `1`:
				setConfirm(`pending`)
				break
			case `2`:
				setConfirm(`approved`)
				break
		}
	}, [])

	const handleSubmission = async () => {
		const params = { id, order_id: confirmId }
		await axios.post(`${API_URL}/set`, params).then(() => location.reload())
	}

	return (
		<Modal
			toggleOpen={toggleOpen}
			modalTitle="Edit Giftcard"
			submit={handleSubmission}>
			<div className="grid grid-cols-2 gap-4">
				<div className="col-span-2">
					<h3 className="font-bold text-2xl">{customer_name}</h3>
				</div>
				<div className="col-span-2 flex items-center gap-4">
					<div className="w-12 h-12 overflow-hidden rounded-full">
						<img
							src={asset_img}
							className="w-full h-full object-cover"
						/>
					</div>
					<div className="">
						<h5 className="text-lg">{asset_name}</h5>
					</div>
				</div>
				<div className="col-span-2">
					<p className="text-sm font-light">{confirm}</p>
				</div>
				<div className="col-span-2">
					<a
						href={proof}
						target="_blank"
						className="text-sm font-light">
						View Proof
					</a>
				</div>
				<div className="col-span-2">
					<h5 className="text-sm font-light">{pastDate(new Date(time_stamp).toISOString())}</h5>
				</div>
				{confirm !== `declined` && (
					<div className="col-span-1 text-center">
						<button
							className="text-sm font-bold cursor-pointer text-zinc-950 outline-none border border-zinc-950 py-2 px-8"
							onClick={() => changeConfirm(`declined`)}>
							Decline
						</button>
					</div>
				)}
				{confirm !== `pending` && (
					<div className="col-span-1 text-center">
						<button
							className="text-sm font-bold cursor-pointer text-zinc-950 outline-none border border-zinc-950 py-2 px-8"
							onClick={() => changeConfirm(`pending`)}>
							Pending
						</button>
					</div>
				)}
				{confirm !== `approved` && (
					<div className="col-span-1 text-center">
						<button
							className="text-sm font-bold cursor-pointer text-zinc-950 outline-none border border-zinc-950 py-2 px-8"
							onClick={() => changeConfirm(`approved`)}>
							Approve
						</button>
					</div>
				)}
			</div>
		</Modal>
	)
}

export default EditHomeTable
