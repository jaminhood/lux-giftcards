import axios from "axios"
import { useEffect, useState } from "react"
import { API_URL } from "../../utils"
import EditHomeTable from "./EditHomeTable"
import HomeTableData from "./HomeTableData"

const HomeTable = () => {
	const [modalIsOpen, setModalIsOpen] = useState(0)
	const [giftcards, setGiftcards] = useState([])
	const [editGiftcard, setEditGiftcard] = useState({})

	const resetModalIsOpen = () => setModalIsOpen(0)

	const handleModalOpen = data => {
		setModalIsOpen(data.id)
		setEditGiftcard(data)
	}

	useEffect(async () => {
		await axios.get(`${API_URL}/get`).then(res => setGiftcards(res.data))
	}, [])

	return (
		<>
			{modalIsOpen !== 0 && (
				<EditHomeTable
					editGiftcard={editGiftcard}
					toggleOpen={resetModalIsOpen}
				/>
			)}
			<div className="py-4">
				{giftcards.length > 0 && (
					<table className="w-full">
						<thead className="text-left border-b border-zinc-300 text-zinc-950">
							<tr>
								<th className="w-[15%] py-2">Customer Name</th>
								<th className="w-[15%] py-2">Time Stamp</th>
								<th className="w-[10%] p-2">Asset Name</th>
								<th className="w-[10%] py-2">Gift Card</th>
								<th className="w-[10%] py-2">Quantity</th>
								<th className="w-[10%] py-2">Price</th>
								<th className="w-[10%] py-2">Proof</th>
								<th className="w-[10%] py-2">Action</th>
							</tr>
						</thead>
						{giftcards.map(data => (
							<HomeTableData
								key={data.id}
								data={data}
								setModalIsOpen={handleModalOpen}
							/>
						))}
					</table>
				)}
			</div>
		</>
	)
}

export default HomeTable
